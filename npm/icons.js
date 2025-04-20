import {args, compile, glob, icons} from 'uikit/build/util.js';

if (args.h || args.help) {
    console.log(`
    
        Builds additional custom uikit icons found in './custom/*/icons'
    
        usage:

        icons.js [custom|name]

        -c|--custom 
            Specify custom folder to look for icons (default: './custom/*/icons')
        -n|--name 
            Specify name regex to match against folder (default: '([a-z]+)/icons$')

    `);
    process.exit(0);
}

const path = args.c || args.custom || 'custom/*/icons';
const match = args.n || args.name || '([a-z]+)/icons$';

await Promise.all((await glob(path)).map(compileIcons));

async function compileIcons(folder) {
    const [, name] = folder.toString().match(new RegExp(match, 'i'));
    return compile(
        'npm/wrapper/icons.js',
        `theme-src/js/libs/uikit-icons-${name}`,
        {
            name,
            replaces: {
                ICONS: await icons(`{node_modules/uikit/src/images/icons,${folder}}/*.svg`)
            }
        }
    );
}
