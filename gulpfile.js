const zip = require("gulp-zip")
const gulp = require('gulp');

async function cleanTask() {
    const del = await import("del")
    return del.deleteAsync('./dist/plugin/**', {force:true});
}

function moveMediaFolderTask() {
    return gulp.src([
        './media/plg_system_concordium/**',
        '!./media/plg_system_concordium/src/**'
    ]).pipe(gulp.dest('./dist/plugin/media'))
}

function movePluginFolderTask() {
    return gulp.src([
        './plugins/system/concordium/**',
    ]).pipe(gulp.dest('./dist/plugin'))
}

function compressTask() {
    return gulp.src('./dist/plugin/**')
        .pipe(zip('plg_system_concordium.zip'))
        .pipe(gulp.dest('./dist'));
}

exports.zip = gulp.series(cleanTask, gulp.parallel(moveMediaFolderTask, movePluginFolderTask), compressTask, cleanTask);
