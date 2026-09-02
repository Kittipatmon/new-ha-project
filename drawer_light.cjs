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

        // Replacements
        drawerPart = drawerPart.replace(/bg-\[#0f1117\]/g, 'bg-white dark:bg-[#0f1117]');
        drawerPart = drawerPart.replace(/text-white font-bold text-xl/g, 'text-gray-900 dark:text-white font-bold text-xl');
        drawerPart = drawerPart.replace(/text-white\/40/g, 'text-gray-500 dark:text-white/40');
        drawerPart = drawerPart.replace(/border-white\/10/g, 'border-gray-100 dark:border-white/10');
        drawerPart = drawerPart.replace(/text-white\/60 hover:text-white hover:bg-white\/10/g, 'text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-white/60 dark:hover:text-white dark:hover:bg-white/10');
        drawerPart = drawerPart.replace(/text-white\/70 hover:text-white hover:bg-white\/5/g, 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5');
        drawerPart = drawerPart.replace(/text-white font-bold text-sm truncate/g, 'text-gray-900 dark:text-white font-bold text-sm truncate');
        drawerPart = drawerPart.replace(/text-red-400 text-\[10px\]/g, 'text-red-500 dark:text-red-400 text-[10px]');
        drawerPart = drawerPart.replace(/bg-white\/10 text-white\/80 hover:bg-white\/15 hover:text-white/g, 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 dark:bg-white/10 dark:text-white/80 dark:hover:bg-white/15 dark:hover:text-white');
        drawerPart = drawerPart.replace(/bg-red-600\/20 text-red-400 text-sm font-semibold hover:bg-red-600\/30 hover:text-red-300/g, 'bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 dark:bg-red-600/20 dark:text-red-400 dark:hover:bg-red-600/30 dark:hover:text-red-300');
        
        // Handle avatar text color for those without photo (like in welcome nav)
        drawerPart = drawerPart.replace(/overflow-hidden text-white text-base/g, 'overflow-hidden text-white text-base'); // This is fine
        
        fs.writeFileSync(filePath, beforeDrawer + drawerPart, 'utf8');
        console.log('Fixed', relPath);
    }
});
