import {args, compile, icons} from 'uikit/build/util.js';

const bundles = getBundleTasks();
const buildAll = args.all || !Object.keys(args).filter(name =>
    !['d', 'debug', 'nominify', 'watch', '_'].includes(name)
).length;

let tasks;
const allTasks = {...bundles};
if (buildAll) {
    tasks = allTasks;
} else {
    tasks = Object.keys(args)
        .map(step => allTasks[step])
        .filter(t => t);
}

await Promise.all(Object.values(tasks).map(task => task()));

function getBundleTasks() {
    return {

        icons: async () => compile('npm/wrapper/icons.js', 'theme-src/js/libs/uikit-icons', {
            name: 'icons',
            replaces: {ICONS: await icons('{node_modules/uikit/src/images,custom}/icons/*.svg')}
        })

    };
}
