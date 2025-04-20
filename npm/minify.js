import {glob} from 'uikit/build/util.js';
import { minify } from 'uglify-js';
import fs from "fs-extra";
import {basename} from "path";

export const { pathExists, readJson } = fs;
const pkg = readJson('package.json')
const year = new Date().getFullYear()
const banner = `/*!
 * Azirax v${pkg.version}
 * Copyright ${year} ${pkg.author}
 * Licensed under MIT License (https://opensource.org/license/mit)
 */`

glob("theme-src/js/*.js", { ignore: './*.min.js' }, function (er, files) {
    files.forEach((x, i) => compile(x, ''));
});

function compile(file, folder) {
    const name = basename(file, '.js');
    const dist = `theme-src/js/${folder}${name}.min.js`;
    const options = {
        toplevel: true,
        compress: {
            passes: 2
        },
        output: {
            beautify: false
        },
        sourceMap: {
            url: "inline"
        }
    }

    fs.readFile(file, 'utf8', (err, data) => {
        if (err) {
            console.error(err);
            return;
        }
        const mini = minify(data, options);

        fs.writeFile(dist, mini.code, function(err) {
            if(err) {
                console.log(err);
            } else {
                console.log(cyan('Compressed file: ' + file));
            }
        });
    });
}

function cyan (str) {
    return `\x1b[1m\x1b[36m${str}\x1b[39m\x1b[22m`;
}
