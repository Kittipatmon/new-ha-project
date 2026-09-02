const fs = require('fs');
const files = {
    'app/Models/Section.php': [
        { old: /protected \$table = 'sections';/g, new: "protected $table = 'appkum_user.sections';" }
    ],
    'app/Models/Division.php': [
        { old: /protected \$table = 'divisions';/g, new: "protected $table = 'appkum_user.divisions';" }
    ],
    'app/Models/Department.php': [
        { old: /protected \$table = 'department';/g, new: "protected $table = 'appkum_user.department';" }
    ],
    'app/Models/UserType.php': [
        { old: /protected \$table = 'user_types';/g, new: "protected $table = 'appkum_user.user_types';" }
    ]
};

for (const [file, replacements] of Object.entries(files)) {
    let content = fs.readFileSync(file, 'utf8');
    for (const {old, new: newStr} of replacements) {
        content = content.replace(old, newStr);
    }
    fs.writeFileSync(file, content, 'utf8');
    console.log('Updated ' + file);
}
