module.exports = function(grunt){

    require('load-grunt-tasks')(grunt); //加载所有的任务
    //require('time-grunt')(grunt); 如果要使用 time-grunt 插件

    grunt.initConfig({
      connect: {
        options: {
                hostname: 'localhost', //默认就是这个值，可配置为本机某个 IP，localhost 或域名 wifi 10.10.112.90  lan:10.10.130.62
                livereload: 35729  //声明给 watch 监听的端口
            },
         server: {
           options: {
             open: true,
             port: 2014,
             base: {
              path: './',
              options: {
                index: 'index.html',
                maxAge: 300000
              }
            }
           }
         }
        },

        watch: {
            livereload: {
                options: {
                    livereload: '<%=connect.options.livereload%>'  //监听前面声明的端口  35729
                },

                files: [  //下面文件的改变就会实时刷新网页
                    '*.html',
                    'css/**/*.css',
                    'js/**/*.js',
                    'images/**/*.*'
                ]
            }
        }
    });

    grunt.registerTask('serve', [
        'connect:server',
        'watch'
    ]);
}
