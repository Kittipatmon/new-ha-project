const fs = require('fs');
const path = require('path');
const layoutsDir = path.join(__dirname, 'resources/views/layouts');
const filesToProcess = [
    'navigation.blade.php',
    'hrrequest/navigation.blade.php',
    'manpower/navigation.blade.php',
    'recruitment/navigation.blade.php',
    'suggestion/navigation.blade.php',
    'training/navigation.blade.php'
];

filesToProcess.forEach(relPath => {
    const filePath = path.join(layoutsDir, relPath);
    if (fs.existsSync(filePath)) {
        let content = fs.readFileSync(filePath, 'utf8');
        
        const fallbackRegex = /class="navbar-link shadow transition"\s*href="\{\{\s*route\('backend\.training\.index'\)\s*\}\}"/g;
        content = content.replace(fallbackRegex, `class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('backend.training.index') }}"`);

        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Updated', relPath);
    }
});
