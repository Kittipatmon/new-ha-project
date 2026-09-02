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

        // Extract the drawer part to only replace within it
        const startMarker = '<div id="mobile-menu"';
        const startIdx = content.indexOf(startMarker);
        if (startIdx === -1) return;

        let beforeDrawer = content.substring(0, startIdx);
        let drawerPart = content.substring(startIdx);

        // Replace the Profile Settings button classes correctly
        drawerPart = drawerPart.replace(
            /bg-white\/10 text-white\/80 text-sm font-semibold hover:bg-white\/15 hover:text-white/g, 
            'bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 hover:text-gray-900 dark:bg-white/10 dark:text-white/80 dark:hover:bg-white/15 dark:hover:text-white'
        );
        
        fs.writeFileSync(filePath, beforeDrawer + drawerPart, 'utf8');
        console.log('Fixed Profile Settings button in', relPath);
    }
});
