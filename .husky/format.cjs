const {execSync} = require('child_process');

const phpFiles = execSync('git diff --cached --name-only --diff-filter=ACM | grep "\\\\.php$" || true').toString().trim();

if (phpFiles) {
    console.log('🛠️ Running Laravel Pint on staged PHP files…');
    const pintCommand = `php vendor/bin/pint ${phpFiles.split('\n').join(' ')}`;
    execSync(pintCommand);

    // Check for $data variable usage
    phpFiles.split('\n').forEach(file => {
        const fileContent = execSync(`cat ${file}`).toString();
        if (fileContent.includes('$data') && !fileContent.includes('@property') && !fileContent.includes(' model ')) {
            console.error(`⚠️ Warning: Generic variable name '$data' found in ${file}. Please use a more descriptive variable name.`);
            process.exit(1);
        }

        const badVariablePatterns = [/\$[a-zA-Z]\b/, /\$([a-z])\1\b/];
        for (const pattern of badVariablePatterns) {
            const match = pattern.exec(fileContent);
            if (match) {
                console.error(`⚠️ Warning: Non-descriptive variable name '${match[0]}' found in ${file}. Please use a more descriptive variable name.`);
                process.exit(1);
            }
        }

    });

    execSync(`git add ${phpFiles.split('\n').join(' ')}`);
    console.log('✅ Pint formatting complete and files re-staged.');
} else {
    console.log('ℹ️ No PHP files staged for Pint.');
}

