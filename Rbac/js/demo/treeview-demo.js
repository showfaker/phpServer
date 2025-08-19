$(function () {

    var defaultData = [
        {
            text: '江苏省',
            href: '#parent1',
            tags: ['4'],
            nodes: [
                {
                    text: '南京市',
                    href: '#child1',
                    tags: ['2'],
                    nodes: 
					[
                        {
                            text: '玄武区',
                            href: '#grandchild1',
                            tags: ['1'],
							nodes: 
							[
								{
									text: '玄武大道',
									href: '#grandgrandchild1',
									tags: ['1']
								},
								{
									text: '中山路',
									href: '#grandgrandchild2',
									tags: ['0']
								}
							]
						},
                        {
                            text: '雨花区',
                            href: '#grandchild2',
                            tags: ['1'],
							nodes: 
							[
								{
									text: '软件大道',
									href: '#grandgrandchild1',
									tags: ['0']
								},
								{
									text: '宁双路',
									href: '#grandgrandchild2',
									tags: ['1']
								}
							]
						}
					]
                },
                {
                    text: '苏州市',
                    href: '#child2',
                    tags: ['2']
              }
            ]
        },
        {
            text: '浙江省',
            href: '#parent2',
            tags: ['0']
          },
        {
            text: '上海市',
            href: '#parent3',
            tags: ['0']
          },
        {
            text: '北京市',
            href: '#parent4',
            tags: ['0']
          },
        {
            text: '安徽省',
            href: '#parent5',
            tags: ['0']
          }
        ];


   
    $('#treeview5').treeview({
		levels: 3,
        color: "#428bca",
        expandIcon: 'glyphicon glyphicon-chevron-right',
        collapseIcon: 'glyphicon glyphicon-chevron-down',
        nodeIcon: 'glyphicon glyphicon-bookmark',
		showTags: true,
        data: defaultData
    });

    

});
