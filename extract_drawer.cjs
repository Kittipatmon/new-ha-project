const fs = require('fs');
const path = require('path');

const layoutsDir = path.join(__dirname, 'resources/views/layouts');
const filesToProcess = [
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
        
        // 1. Move </nav> from after the drawer/backdrop to before the backdrop
        // We know the backdrop starts with <!-- Mobile Menu Backdrop -->
        
        // Remove </nav> near the end, but before <!-- Login Modal --> if it exists, or just before <script>
        // Better: let's replace `    <!-- Mobile Menu Backdrop -->` with `</nav>\n\n    <!-- Mobile Menu Backdrop -->`
        if (!content.includes('</nav>\n\n    <!-- Mobile Menu Backdrop -->') && 
            !content.includes('</nav>\n    <!-- Mobile Menu Backdrop -->') &&
            !content.includes('</nav>\r\n\r\n    <!-- Mobile Menu Backdrop -->')) {
            content = content.replace('    <!-- Mobile Menu Backdrop -->', '</nav>\n\n    <!-- Mobile Menu Backdrop -->');
        }
        
        // Remove the old </nav> tag that is now redundant at the bottom of the nav section
        // Look for </nav> followed by <!-- Login Modal --> or <script> or end of file
        content = content.replace(/<\/nav>\s*<!-- Login Modal -->/, '<!-- Login Modal -->');
        content = content.replace(/<\/nav>\s*<script>/, '<script>');
        
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Fixed', relPath);
    }
});
