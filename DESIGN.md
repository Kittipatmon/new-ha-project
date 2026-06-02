---
name: HR System
description: ระบบบริหารจัดการทรัพยากรบุคคล
colors:
  primary: "#B21F24"
  neutral-bg: "#F9FAFB"
  neutral-surface: "#FFFFFF"
  neutral-ink: "#111827"
typography:
  display:
    fontFamily: "Figtree, sans-serif"
    fontSize: "clamp(2rem, 5vw, 3rem)"
    fontWeight: 700
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Figtree, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 600
  body:
    fontFamily: "Figtree, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: "1.5"
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
  pill: "9999px"
spacing:
  sm: "8px"
  md: "16px"
  lg: "24px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#FFFFFF"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "#991B1F"
  card:
    backgroundColor: "{colors.neutral-surface}"
    rounded: "{rounded.lg}"
    padding: "{spacing.lg}"
---

# Design System: HR System

## 1. Overview

**Creative North Star: "The Modern Professional Navigator"**

ระบบถูกออกแบบมาให้เป็นศูนย์รวมที่ทำงานได้อย่างรวดเร็ว ทันสมัย และเป็นทางการ แต่ในขณะเดียวกันก็มีความนุ่มนวล เป็นมิตรกับผู้ใช้งาน เน้นความชัดเจนของข้อมูลเพื่อการเข้าถึงที่รวดเร็วที่สุด การออกแบบจะไม่ใช้ความซับซ้อนของการตกแต่งมาแย่งความสนใจจากเนื้อหาหลัก สิ่งที่ระบบนี้หลีกเลี่ยงอย่างเด็ดขาดคือรูปแบบแอปพลิเคชันองค์กรยุคเก่าที่ดูน่าเบื่อ การใช้สีสันฉูดฉาดเกินความจำเป็น และโหมดมืดที่อ่านยาก

**Key Characteristics:**
- **Clarity First:** ข้อมูลและการนำทางต้องชัดเจนและเข้าถึงง่ายที่สุด
- **Soft Professionalism:** มีความเป็นมืออาชีพแต่เข้าถึงง่าย ไม่แข็งกระด้าง
- **Efficient Flow:** จัดกลุ่มและระยะห่างอย่างเป็นระเบียบ เพื่อลดความซับซ้อน

## 2. Colors

ชุดสีที่เรียบง่าย เน้นความสว่างและสะอาดตา โดยใช้สีประจำองค์กรเป็นจุดดึงความสนใจหลัก

### Primary
- **Vibrant Corporate Red** (#B21F24): ใช้สำหรับปุ่มหลัก (Primary Actions), สถานะสำคัญ, หรือลิงก์ที่ต้องการเน้นย้ำ เป็นสีที่กระตุ้นการทำงานและสร้างความมั่นใจ

### Neutral
- **Clean Background** (#F9FAFB): สีพื้นหลังหลักของหน้าจอ เพื่อให้ความรู้สึกสว่างและสบายตา
- **Surface White** (#FFFFFF): สีพื้นหลังของการ์ดและคอนเทนเนอร์ เพื่อแยกส่วนข้อมูลออกจากพื้นหลังหลัก
- **Ink Dark** (#111827): สีหลักสำหรับข้อความ เพื่อคอนทราสต์ที่ชัดเจนและอ่านง่ายที่สุดบนพื้นสว่าง

**The Selective Accent Rule.** ใช้ Vibrant Corporate Red เฉพาะจุดที่ต้องการให้ผู้ใช้โต้ตอบหรือจุดสังเกตสำคัญเท่านั้น ห้ามใช้สีแดงเป็นพื้นหลังพื้นที่ขนาดใหญ่

## 3. Typography

**Display Font:** Figtree, sans-serif
**Body Font:** Figtree, sans-serif

**Character:** ทันสมัย เป็นทางการแต่อ่านง่าย และมีความนุ่มนวล ไม่แข็งกระด้าง

### Hierarchy
- **Display** (Bold 700, clamp(2rem, 5vw, 3rem), tight letter-spacing): สำหรับหัวข้อหน้าหลัก หรือตัวเลขสถิติที่สำคัญ
- **Headline** (Semi-Bold 600, 1.5rem, 1.3): สำหรับหัวข้อของแต่ละส่วน (Sections) หรือการ์ด
- **Title** (Medium 500, 1.125rem, 1.4): สำหรับหัวข้อย่อยหรือชื่อรายการ
- **Body** (Regular 400, 1rem, 1.5): สำหรับข้อความเนื้อหาหลักทั่วไป ความยาวบรรทัดควรอยู่ที่ 65–75 ตัวอักษร
- **Label** (Medium 500, 0.875rem): สำหรับป้ายกำกับ, คำแนะนำในฟอร์ม, หรือปุ่ม

**The Readable Contrast Rule.** ข้อความหลักต้องมีระดับคอนทราสต์ที่ผ่านเกณฑ์ ห้ามใช้ตัวอักษรสีเทาอ่อนบนพื้นหลังสีเทาหรือสีครีมที่ทำให้กลืนกัน

## 4. Elevation

ระบบใช้ความแบนราบเป็นหลัก ไม่มีเงาที่ลึกหรือซับซ้อน เพื่อเน้นความสะอาดตาและความเร็วในการรับรู้

**The Flat-By-Default Rule.** พื้นผิวต่างๆ จะต้องแบนราบที่สถานะปกติ (Flat Design) ใช้เพียงเส้นขอบบางๆ หรือสีพื้นหลังที่แตกต่างกัน (Tonal separation) ในการแบ่งสัดส่วนของการ์ดหรือคอนเทนเนอร์ ห้ามใช้ Drop shadow ที่หนักเกินไป

## 5. Components

### Buttons
- **Shape:** มุมโค้งมนนุ่มนวล (8px radius) เพื่อให้ความรู้สึกเป็นมิตร
- **Primary:** สี Vibrant Corporate Red (#B21F24) พร้อมข้อความสีขาว
- **Hover / Focus:** สีพื้นหลังแดงเข้มขึ้น (#991B1F) เล็กน้อย ไม่มีการขยับปุ่ม (No transform) เพื่อรักษาความนิ่งของ Flat Design
- **Secondary / Ghost:** พื้นหลังโปร่งใส หรือสีเทาอ่อน สำหรับการกระทำรอง

### Cards / Containers
- **Corner Style:** 12px radius เพื่อรับกับความนุ่มนวลของภาพรวม
- **Background:** พื้นผิวสีขาว (#FFFFFF) บนพื้นหลังหลักสีเทาอ่อน
- **Shadow Strategy:** ไม่มีเงา ใช้เส้นขอบสีเทาอ่อนมากๆ หรืออาศัยความต่างของสีพื้นหลังแทน
- **Internal Padding:** กว้างขวาง (24px) เพื่อให้ข้อมูลไม่อึดอัด

### Inputs / Fields
- **Style:** เส้นขอบสีเทาอ่อน พื้นหลังขาว มุมโค้ง 8px
- **Focus:** เปลี่ยนสีเส้นขอบเป็น Vibrant Corporate Red หรือสีน้ำเงินเข้มที่เป็นมิตร ไม่หนาจนเกินไป

### Navigation
- **Style:** แถบนำทางด้านข้างหรือด้านบนที่เรียบง่าย ไฮไลต์เมนูที่แอคทีฟด้วยสีแดงองค์กรบางส่วน (เช่น แถบเส้นด้านข้างหรือไอคอน)

## 6. Do's and Don'ts

### Do:
- **Do** ใช้พื้นที่ว่าง (Whitespace) ในการจัดกลุ่มข้อมูล แทนการตีเส้นกรอบเยอะๆ
- **Do** ให้ความสำคัญกับ Contrast ของตัวอักษร โดยเฉพาะฟอร์มที่มีการกรอกข้อมูล
- **Do** ทำให้ส่วนของหน้าจอที่สามารถคลิกได้ (Interactive) แตกต่างจากข้อมูลทั่วไปอย่างชัดเจน

### Don't:
- **Don't** ออกแบบ UI ให้ดูเก่าเหมือนแอปพลิเคชันองค์กรยุค 2010s
- **Don't** ใช้สีสันที่ฉูดฉาดเกินไปหรือ Gradient หนักๆ ที่ดูไม่เป็นทางการ
- **Don't** ใช้ Dark Mode ที่มืดทึบจนอ่านยาก หรือใช้สีที่ทำให้เกิดความรู้สึกอึดอัด
- **Don't** ใช้ความโค้งมนที่มากเกินไปจนกลายเป็นวงรี (Full pill) สำหรับการ์ดหรือคอนเทนเนอร์ใหญ่ (จำกัดแค่ 12px)
