const fs = require('fs');
const files = {
    'app/Models/Section.php': [
        { old: /protected \$table = 'appkum_user.sections';/g, new: "protected $table = 'sections';" }
    ],
    'app/Models/Division.php': [
        { old: /protected \$table = 'appkum_user.divisions';/g, new: "protected $table = 'divisions';" }
    ],
    'app/Models/Department.php': [
        { old: /protected \$table = 'appkum_user.department';/g, new: "protected $table = 'department';" }
    ],
    'app/Models/UserType.php': [
        { old: /protected \$table = 'appkum_user.user_types';/g, new: "protected $table = 'user_types';" }
    ]
};

for (const [file, replacements] of Object.entries(files)) {
    let content = fs.readFileSync(file, 'utf8');
    for (const {old, new: newStr} of replacements) {
        content = content.replace(old, newStr);
    }
    fs.writeFileSync(file, content, 'utf8');
    console.log('Reverted ' + file);
}
