# คู่มือการติดตั้งและตั้งค่า: Microsoft Graph API + Queue + Mail Log
**ระบบสรรหาและว่าจ้างบุคลากร (Recruitment System) - Kumwell Corporation Public Company Limited**

เอกสารนี้จัดทำขึ้นสำหรับ **ฝ่ายเทคโนโลยีสารสนเทศ (IT Department)** และ **ผู้พัฒนาระบบ (Developers)** เพื่อใช้ในการตั้งค่าเชื่อมต่อระบบส่งอีเมลผ่าน **Microsoft Graph API (OAuth 2.0)**, การเปิดใช้งาน **Queue Worker**, และการใช้งานระบบ **Mail Log**

---

## สารบัญ
1. [ภาพรวมสถาปัตยกรรม (Architecture Overview)](#1-ภาพรวมสถาปัตยกรรม-architecture-overview)
2. [ขั้นตอนสำหรับฝ่าย IT บน Azure Portal (Microsoft Entra ID)](#2-ขั้นตอนสำหรับฝ่าย-it-บน-azure-portal-microsoft-entra-id)
3. [การตั้งค่าในไฟล์ .env ของระบบ](#3-การตั้งค่าในไฟล์-env-ของระบบ)
4. [การทำงานและการรัน Queue Worker](#4-การทำงานและการรัน-queue-worker)
5. [การใช้งานระบบประวัติการส่งอีเมล (Mail Logs)](#5-การใช้งานระบบประวัติการส่งอีเมล-mail-logs)
6. [การแก้ไขปัญหาที่พบบ่อย (Troubleshooting)](#6-การแก้ไขปัญหาที่พบบ่อย-troubleshooting)

---

## 1. ภาพรวมสถาปัตยกรรม (Architecture Overview)

ระบบถูกออกแบบให้ทำงานแบบ **Enterprise Modern Architecture**:
* **Microsoft Graph API:** เมื่อเจ้าหน้าที่ HA กดปุ่ม "เชื่อมต่อ Microsoft 365" ระบบจะขอสิทธิ์ OAuth 2.0 เพื่อส่งอีเมลในนามของเจ้าหน้าที่ท่านนั้นโดยตรง (เมลที่ส่งจะไปปรากฏในกล่อง **Sent Items ใน Outlook** ของเจ้าหน้าที่ด้วย)
* **Hybrid Fallback:** หากเจ้าหน้าที่ท่านใดยังไม่ได้เชื่อมต่อ Microsoft 365 หรือระบบ Graph API เกิดเหตุขัดข้อง ระบบจะสลับไปส่งผ่าน **SMTP Fallback** ให้อัตโนมัติโดยไม่ทำให้กระบวนการทำงานสะดุด
* **Queue System:** เมลทุกฉบับจะถูกส่งเข้าคิวเบื้องหลัง (Background Job) ทำให้หน้าเว็บตอบสนองรวดเร็วใน 0.1 วินาที ไม่ต้องหมุนรอการเชื่อมต่อภายนอก
* **Mail Log:** บันทึกประวัติการส่ง วันเวลา ผู้ส่ง ผู้รับ หัวข้อ และสถานะความสำเร็จ/ล้มเหลว พร้อมปุ่มกดส่งซ้ำ (Retry)

---

## 2. ขั้นตอนสำหรับฝ่าย IT บน Azure Portal (Microsoft Entra ID)

ฝ่าย IT ของ Kumwell ดำเนินการสร้าง **App Registration** เพียงครั้งเดียวตามขั้นตอนดังนี้:

### ขั้นตอนที่ 1: เข้าสู่ระบบ Azure Portal
1. เข้าไปที่ [https://portal.azure.com](https://portal.azure.com) ด้วยบัญชี Global Admin หรือ Cloud Application Admin ของ Kumwell
2. ค้นหาบริการ **Microsoft Entra ID** (หรือเดิมคือ *Azure Active Directory*)
3. ไปที่เมนู **App registrations** ในแถบเมนูด้านซ้าย -> คลิก **+ New registration**

### ขั้นตอนที่ 2: กรอกข้อมูลลงทะเบียนแอป (Register an application)
* **Name:** `Kumwell HR Recruitment Mailer` (หรือชื่อตามนโยบายองค์กร)
* **Supported account types:** เลือก **Accounts in this organizational directory only (Kumwell Corporation Public Company Limited only - Single tenant)**
* **Redirect URI (optional):**
  * เลือก Platform เป็น **Web**
  * ใส่ URL: `http://localhost:8000/auth/microsoft/callback` (สำหรับทดสอบในเครื่อง)
  * *และสามารถเพิ่ม URL เซิร์ฟเวอร์จริงได้ เช่น:* `https://hr.kumwell.com/auth/microsoft/callback`
* คลิกปุ่ม **Register**

### ขั้นตอนที่ 3: คัดลอกค่า Application ID & Tenant ID
ในหน้า **Overview** ของแอป ให้คัดลอกค่า 2 ตัวนี้เก็บไว้:
1. **Application (client) ID:** *(เช่น `e87b21a3-xxxx-xxxx-xxxx-xxxxxxxxxxxx`)*
2. **Directory (tenant) ID:** *(เช่น `4d2f9b1c-xxxx-xxxx-xxxx-xxxxxxxxxxxx`)*

### ขั้นตอนที่ 4: กำหนดสิทธิ์ API Permissions
1. ไปที่เมนู **API permissions** ด้านซ้าย -> คลิก **+ Add a permission**
2. เลือก **Microsoft Graph** -> เลือก **Delegated permissions**
3. ค้นหาและติ๊กเลือกสิทธิ์ต่อไปนี้:
   * `Mail.Send` *(อนุญาตให้ส่งอีเมลในนามผู้ใช้ที่ล็อกอิน)*
   * `User.Read` *(อนุญาตให้อ่านข้อมูลโปรไฟล์เบื้องต้น)*
   * `offline_access` *(อนุญาตให้ใช้ Refresh Token ต่ออายุการใช้งานเบื้องหลัง)*
4. คลิกปุ่ม **Add permissions**
5. *(แนะนำ)* คลิกปุ่ม **Grant admin consent for Kumwell Corporation** เพื่อให้เจ้าหน้าที่ HA สามารถเชื่อมต่อได้ทันทีโดยไม่ต้องกดยินยอมรายบุคคล

### ขั้นตอนที่ 5: สร้าง Client Secret
1. ไปที่เมนู **Certificates & secrets** ด้านซ้าย
2. แท็บ **Client secrets** -> คลิก **+ New client secret**
3. Description: `HR System Web Secret`
4. Expires: เลือกระยะเวลาตามนโยบายความปลอดภัย (เช่น 24 months หรือตามที่กำหนด)
5. คลิก **Add**
6. **คัดลอกค่าในช่อง "Value" ทันที** *(หมายเหตุ: หน้านี้จะแสดงรหัสลับแค่ครั้งเดียวหลังสร้าง)*

---

## 3. การตั้งค่าในไฟล์ .env ของระบบ

เปิดไฟล์ `.env` ของโปรเจกต์ และระบุค่าที่ได้จากขั้นตอนที่ 2:

```env
# =========================================================================
# Microsoft 365 / Microsoft Graph API Configuration
# =========================================================================
MICROSOFT_CLIENT_ID=วาง-Application-Client-ID-ตรงนี้
MICROSOFT_CLIENT_SECRET=วาง-Client-Secret-Value-ตรงนี้
MICROSOFT_TENANT_ID=วาง-Directory-Tenant-ID-ตรงนี้
MICROSOFT_REDIRECT_URI="${APP_URL}/auth/microsoft/callback"

# =========================================================================
# Queue Configuration (ประมวลผลการส่งเมลเบื้องหลัง)
# =========================================================================
QUEUE_CONNECTION=database
```

---

## 4. การทำงานและการรัน Queue Worker

เมื่อมีคำสั่งส่งเมล (เช่น กดนัดสัมภาษณ์ หรือแจ้งผล) ระบบจะผลักงานเข้าตาราง `jobs` ในฐานข้อมูลทันที

### 4.1 การรัน Worker ในโหมดพัฒนา (Development / Testing)
เปิด Terminal หรือ Command Prompt ในโฟลเดอร์โปรเจกต์ แล้วรันคำสั่ง:
```bash
php artisan queue:work
```
> [!NOTE]
> ในขณะที่คำสั่งนี้รันอยู่ เมลที่ถูกส่งเข้ามาในคิวจะถูกประมวลผลและส่งออกทาง Microsoft Graph API ทันที

### 4.2 การตั้งค่า Worker บน Production (Windows Server / XAMPP)
เพื่อไม่ให้ต้องเปิดหน้าต่าง CMD ทิ้งไว้ แนะนำ 2 วิธี:

#### วิธีที่ A: ใช้ NSSM (Non-Sucking Service Manager) ติดตั้งเป็น Windows Service (แนะนำที่สุด)
1. ดาวน์โหลด [NSSM](https://nssm.cc/)
2. รันคำสั่งสร้าง Service:
   ```cmd
   nssm install LaravelQueueWorker "C:\xampp\php\php.exe" "c:\xampp\htdocs\ha-project\artisan queue:work --tries=3"
   nssm start LaravelQueueWorker
   ```
   *Service จะรันอยู่เบื้องหลังตลอดเวลา และรีสตาร์ตตัวเองอัตโนมัติหากเครื่องเปิดใหม่*

#### วิธีที่ B: ใช้ Windows Task Scheduler
ตั้งเวลาให้รัน `php artisan queue:work --stop-when-empty` ทุกๆ 1 นาที หรือรัน Background Script

---

## 5. การใช้งานระบบประวัติการส่งอีเมล (Mail Logs)

สามารถเข้าใช้งานผ่านเมนูระบบหลังบ้าน:
* URL: `http://localhost:8000/backend/recruitment/mail-logs`
* หรือคลิกที่เมนู **"ประวัติส่งอีเมล"** บนแถบเมนูหลัก

### ความสามารถของหน้า Mail Logs:
1. **สถิติภาพรวม (Statistics):** แสดงยอดรวมทั้งหมด, จำนวนที่ส่งสำเร็จ, จำนวนที่ล้มเหลว, ยอดรอในคิว, และสัดส่วนช่องทาง (Graph API vs SMTP)
2. **ค้นหาและฟิลเตอร์ (Search & Filters):** ค้นหาด้วยชื่อผู้รับ, อีเมล, หัวข้อ หรือกรองตามประเภทของอีเมลและสถานะ
3. **ตรวจสอบข้อผิดพลาด (Error Modal):** หากมีฉบับใดล้มเหลว จะมีไอคอนเตือนสีแดง เมื่อคลิกจะแสดง Error Message ที่ได้รับจาก Microsoft หรือ SMTP อย่างละเอียด
4. **ปุ่มส่งซ้ำ (Retry Button):** สามารถกดปุ่มหมุนวนสีส้ม เพื่อสั่งให้นำอีเมลฉบับนั้นเข้าคิวส่งใหม่อีกครั้งได้ทันที

---

## 6. การแก้ไขปัญหาที่พบบ่อย (Troubleshooting)

| ปัญหา | สาเหตุ | วิธีแก้ไข |
|---|---|---|
| **กดเชื่อมต่อแล้วขึ้นข้อความแจ้งเตือนสีแดงเรื่อง Client ID** | ยังไม่ได้ใส่ค่าใน `.env` | ตรวจสอบไฟล์ `.env` ว่าได้ใส่ `MICROSOFT_CLIENT_ID` และ `MICROSOFT_CLIENT_SECRET` แล้วหรือยัง |
| **Error: Redirect URI mismatch** | URL ใน `.env` ไม่ตรงกับที่กรอกใน Azure | ตรวจสอบว่าใน Azure App Registration -> Authentication มี URL `.../auth/microsoft/callback` ตรงกับ `${APP_URL}` หรือไม่ |
| **อีเมลค้างอยู่ที่สถานะ "อยู่ในคิว" (Queued) ไม่ส่งออก** | ยังไม่ได้เปิดรัน Queue Worker | รันคำสั่ง `php artisan queue:work` ใน Terminal หรือตรวจสอบสถานะ Service |
| **Microsoft Graph API Error: Token expired** | Access Token หมดอายุและ Refresh ไม่สำเร็จ | ให้เจ้าหน้าที่คลิกที่ชื่อโปรไฟล์มุมขวาบน -> กด "ยกเลิก" แล้วคลิก "เชื่อมต่อ Microsoft 365" ใหม่อีกครั้ง |
| **เจ้าหน้าที่ยังไม่ได้เชื่อมต่อ Microsoft แต่อยากส่งเมล** | ระบบมี Hybrid Fallback | ระบบจะสลับไปส่งผ่าน SMTP ตามที่ระบุใน `.env` ให้อัตโนมัติโดยงานไม่ค้าง |

---

*จัดทำโดย: ทีมพัฒนาระบบสารสนเทศ Kumwell*  
*ปรับปรุงล่าสุด: กันยายน 2026*
