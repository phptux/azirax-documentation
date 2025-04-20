import rtlcss from 'rtlcss';
import {dirname, basename} from 'path';
import {args, glob, minify, pathExists, read, readJson, renderLess, write} from 'uikit/build/util.js';

export const banner = `/*! Azirax with UIkit ${await getVersion()} | ${new Date().getFullYear()} | MIT License */\n`;
const {rtl} = args;
const develop = args.develop || args.debug || args.d || args.nominify;
const sources = [
    //{src: './node_modules/uikit/src/less/uikit.less', dist: `public/assets/css/uikit-core${rtl ? '-rtl' : ''}.css`},
    //{src: './node_modules/uikit/src/less/uikit.theme.less', dist: `public/assets/css/uikit${rtl ? '-rtl' : ''}.css`}
];

const themes = await pathExists('themes.json') ? await readJson('themes.json') : {};

for (const src of await glob('custom/*.less')) {
    const theme = basename(src, '.less');
    const dist = `theme-src/css/${theme}${rtl ? '-rtl' : ''}.css`;

    themes[theme] = {css: `../${dist}`};

    sources.push({src, dist});
}

await Promise.all(sources.map(({src, dist}) => compile(src, dist, develop, rtl)));

if (!rtl && (Object.keys(themes).length || !await pathExists('themes.json'))) {
    await write('themes.json', JSON.stringify(themes));
}

async function compile(file, dist, develop, rtl) {

    const less = await read(file);

    let output = (await renderLess(less, {
        relativeUrls: true,
        rootpath: './',
        paths: ['node_modules/uikit/src/less/', 'custom/']
    })).replace(/images\//g, 'img/')
        .replace(/custom\//g, '/');

    if (rtl) {
        output = rtlcss.process(
            output,
            {
                stringMap: [{
                    name: 'previous-next',
                    priority: 100,
                    search: ['previous', 'Previous', 'PREVIOUS'],
                    replace: ['next', 'Next', 'NEXT'],
                    options: {
                        scope: '*',
                        ignoreCase: false
                    }
                }]
            },
            [
                {
                    name: 'customNegate',
                    priority: 50,
                    directives: {
                        control: {},
                        value: []
                    },
                    processors: [
                        {
                            expr: ['--uk-position-translate-x', 'stroke-dashoffset'].join('|'),
                            action(prop, value, context) {
                                return {prop, value: context.util.negate(value)};
                            }
                        }
                    ]
                }
            ],
            {
                pre(root, postcss) {
                    root.prepend(postcss.comment({text: 'rtl:begin:rename'}));
                    root.append(postcss.comment({text: 'rtl:end:rename'}));
                }
            }
        );
    }

    await write(dist, banner + output);

    if (!develop) {
        await minify(dist);
    }

}

export async function getVersion() {
    return (await readJson('package.json')).version;
}
