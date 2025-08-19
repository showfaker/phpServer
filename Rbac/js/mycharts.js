if($(window).width() > 991) {
	var total = window.innerHeight;
	document.getElementById("title").style.height = total * 0.1 + "px";
	document.getElementById("main").style.height = total * 0.9 + "px";
	title = document.getElementById("title");
	main = document.getElementById("main");

	box02 = document.getElementById("box02");
	box03 = document.getElementById("box03");
	box04 = document.getElementById("box04");
	box05 = document.getElementById("box05");
	title_h = title.offsetHeight;
	main_h = main.offsetHeight;

	box02_h = box02.offsetHeight;
	box03_h = box03.offsetHeight;
	box04_h = box04.offsetHeight;
	document.getElementById("box01").style.height = main_h * 0.3 + "px";
	document.getElementById("box8-box").style.height = main_h * 0.3 + "px";
	document.getElementById("box12-box").style.height = main_h * 0.3 + "px";
	box02.style.height = (main_h * 0.6 + 10) + "px";
	document.getElementById("box9-box").style.height = main_h * 0.3 + "px";
	box03.style.height = main_h * 0.3 + "px";
	box04.style.height = main_h * 0.3 + "px";
	box05.style.height = main_h * 0.3 + "px";
	box01 = document.getElementById("box01");
	box01_h = box01.offsetHeight;
	document.getElementById("total-mn1").style.height = box01_h * 0.02 + "px";
	document.getElementById("total-mn2").style.height = box01_h * 0.02 + "px";
	document.getElementById("live-box").style.height = box01_h * 0.05 + "px";
	document.getElementById("ym-menu").style.height = box03_h * 0.1 + "px";
};
var app = angular.module('myApp', []);
app.controller('customersCtrl', function($scope, $http) {
	$http({
		method: 'get',
		url: 'data/da.json'
	}).then(function(res) {
		$scope.listHead = res.data.listHead; //数据列表-头
		$scope.listContent = res.data.listContent; //数据列表
		$scope.listData1 = res.data.listData1; //数据列表
		
		
		//中左一					
		var worldMapContainer2 = document.getElementById('box2');
		var box01 = document.getElementById("box9");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			worldMapContainer2.style.width = box01_w * 0.3 + 'px';
			worldMapContainer2.style.height = box01_h * 0.8 + 'px';
			worldMapContainer2.style.float = "left";
			worldMapContainer2.style.backgroundColor = 'red';
		};
		//设置容器高宽
		resizeWorldMapContainer2();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer2);
		var option = {
			color: ['#38b3f1'],
			tooltip: {
				trigger: 'axis',
				axisPointer: { // 坐标轴指示器，坐标轴触发有效
					type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
				}
			},
			title: {
				text:'今日实时能耗',
				textStyle: {
					color: '#A6CEE2',
					fontStyle:'normal',
					//字体粗细 'normal','bold','bolder','lighter',100 | 200 | 300 | 400...
					fontWeight:'normal',
					//字体系列
					fontFamily:'sans-serif',
					//字体大小
			　　　　fontSize:9
				},
			},
			
			grid: {
				top: '20%',
				left: '3%',
				right: '3%',
				bottom: '6%',
				containLabel: true
			},
			
			xAxis: [{
				type: 'category',
				data: res.data.titleList,
				axisTick:false,
				axisLine:{
                        lineStyle:{
                            color:'#0E4892',
                            width:1,//这里是为了突出显示加上的
                        }
                },
				axisLabel:{
						rotate:30,
						textStyle:{
							 color:"#FEFFFF",
							 fontSize:9
						},
						interval:0
					},
				boundaryGap : false,
			}],
			yAxis: [{
				axisLine: {
					show: false
				},
				splitLine:{
                        lineStyle:{
                            color:['#053A62'],
                            width:1,//这里是为了突出显示加上的
                        }
                },
				axisTick:false,
				type: 'value',
				axisLabel:{
						textStyle:{
							 color:"#0087A7"
						}
					},
			}],
			series: [{
				name: '直接访问',
				type: 'bar',
				barWidth: '40%',
				itemStyle: {
								normal: {
									color: new echarts.graphic.LinearGradient(
										0, 0, 0, 1,
										[
											{offset: 0, color: '#55F1E6'},
											{offset: 0.5, color: '#50D2EA'},
											{offset: 1, color: '#48AEF0'}
										]
									)
								}
							},
				data: res.data.dataList
			}]
		};
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer2();
			myChart.resize();
		};
		
		
		
		var box01 = document.getElementById("box05");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//在线情况					
		var worldMapContainer4 = document.getElementById('box4');
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4 = function() {
			worldMapContainer4.style.width = box01_w * 0.48 + 'px';
			worldMapContainer4.style.height = box01_h * 0.55 + 'px';
			worldMapContainer4.style.float = 'left';
		};
		//设置容器高宽
		resizeWorldMapContainer4();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer4);
		// 指定图表的配置项和数据

		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			textStyle: {
				color: '#FEFFFF'
			},
			legend: {
				show:false,
				data: res.data.titleList2,
			},
			calculable: true,
			series: [{
				color: ['#E5BD66', '#2FA9D2'],
				name: '在线情况',
				type: 'pie',
				radius: '72%',
				center: ['50%', '45%'],
				data: function() {
					var serie = [];
					var item = {
						name: res.data.titleList2[0],
						value: res.data.dataList2[0],
						selected:false,
						label: {
							normal: {
								position: 'inner'
							}
						}
					};
					serie.push(item);
					var item = {
						name: res.data.titleList2[1],
						value: res.data.dataList2[1],
						selected:true,
						label: {
							normal: {
								position: 'inner'
							}
						}
					};
					serie.push(item);
					return serie;
				}()

			}]
		};
		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
		
		
		//在线情况					
		var worldMapContainer41 = document.getElementById('box41');
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer41 = function() {
			worldMapContainer41.style.width = box01_w * 0.48 + 'px';
			worldMapContainer41.style.height = box01_h * 0.55 + 'px';
			worldMapContainer41.style.float = 'left';
		};
		//设置容器高宽
		resizeWorldMapContainer41();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer41);
		// 指定图表的配置项和数据

		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			textStyle: {
				color: '#FEFFFF'
			},
			/*
			legend: {
				itemWidth: 10,
				itemHeight: 10,
				itemGap: 10,
				x: 'right',
				y: 'top',
				textStyle: {
					fontSize: 12,
					color: '#9FC4E1'
				},
				data: res.data.titleList2,
			},
			*/
			calculable: true,
			series: [{
				color: ['#E5BD66', '#2FA9D2'],
				name: '在线情况',
				type: 'pie',
				radius: '72%',
				center: ['50%', '45%'],
				data: function() {
					var serie = [];
					var item = {
						name: res.data.titleList2[0],
						value: res.data.dataList2[0],
						selected:false,
						label: {
							normal: {
								position: 'inner'
							}
						}
					};
					serie.push(item);
					var item = {
						name: res.data.titleList2[1],
						value: res.data.dataList2[1],
						selected:true,
						label: {
							normal: {
								position: 'inner'
							}
						}
					};
					serie.push(item);
					return serie;
				}()

			}]
		};
		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer4();
			myChart.resize();
		};
		
		
		//左三
		var worldMapContainer8 = document.getElementById('box8');
		box8_box = document.getElementById("box12-box");
		box8_box_h = box8_box.offsetHeight;
		box8_box_w = box8_box.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer8 = function() {
			worldMapContainer8.style.width = box8_box_w * 0.96 + 'px';
			worldMapContainer8.style.height = box8_box_h * 0.87 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer8();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer8);
		// 指定图表的配置项和数据
		var option = {
			grid: {
				left: '5%',
				right: '5%',
				bottom: '15%',
				top: '15%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					axisLabel:{
						textStyle:{
							 color:"#FEFFFF"
						}
					},
					boundaryGap : true,
					axisTick:false,
					axisLine:{
							lineStyle:{
								color:'#0E4892',
								width:1,
							}
					},
					splitLine:{
							show:true,
							lineStyle:{
								color:['#1551B1'],
								width:1,
								type:"dashed"
							}
					},
					data : ['水','电','气']
				}
			],
			yAxis : [
				{
					axisLabel:{
						textStyle:{
							 color:"#0087A7"
						}
					},
					axisLine: {
						show: false
					},
					splitLine:{
							lineStyle:{
								color:['#053A62'],
								width:1,
							}
					},
					axisTick:false,
					type : 'value'
				}
			],
			series : [
				{
					type:'line',
					stack: '总量',
					label: {
						normal: {
							show: true,
							position: 'top'
						}
					}, 
					smooth: true,
					symbol: 'emptyCircle',
					symbolSize: 5,
					sampling: 'average',
					itemStyle: {
						normal: {
							color: '#22D049'
						}
					},
					stack: 'a',
					areaStyle: {
						normal: {
							color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
								offset: 0,
								color: '#136242'
							}, {
								offset: 1,
								color: '#001D2F'
							}])
						}
					},
					data:[120, 170, 150]
				}
			]
		};


		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
		

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer8();
			myChart.resize();
		};
		
		
		
		
		//右上
		var worldMapContainer6 = document.getElementById('box6');
		box6_box = document.getElementById("box03");
		box6_box_h = box6_box.offsetHeight;
		box6_box_w = box6_box.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer6 = function() {
			worldMapContainer6.style.width = box6_box_w * 0.96 + 'px';
			worldMapContainer6.style.height = box6_box_h * 0.87 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer6();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer6);
		// 指定图表的配置项和数据
		var option = {
			grid: {
				left: '5%',
				right: '5%',
				bottom: '15%',
				top: '15%',
				containLabel: true
			},
			xAxis : [
				{
					type : 'category',
					axisLabel:{
						textStyle:{
							color:"#FEFFFF"
						},
						interval: 0
					},
					boundaryGap : true,
					axisTick:false,
					axisLine:{
							lineStyle:{
								color:'#0E4892',
								width:1,
							}
					},
					splitLine:{
							show:true,
							lineStyle:{
								color:['#1551B1'],
								width:1,
								type:"dashed"
							}
					},
					data : ['0:00','4:00','8:00','12:00','16:00','20:00','24:00']
				}
			],
			yAxis : [
				{
					axisLabel:{
						textStyle:{
							 color:"#0087A7"
						}
					},
					axisLine: {
						show: false
					},
					splitLine:{
							lineStyle:{
								color:['#053A62'],
								width:1,
							}
					},
					axisTick:false,
					type : 'value'
				}
			],
			series : [
				{
					type:'line',
					stack: '总量',
					label: {
						normal: {
							show: true,
							position: 'top'
						}
					}, 
					smooth: true,
					symbol: 'emptyCircle',
					symbolSize: 5,
					sampling: 'average',
					itemStyle: {
						normal: {
							color: '#22D049'
						}
					},
					stack: 'a',
					areaStyle: {
						normal: {
							color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
								offset: 0,
								color: '#136242'
							}, {
								offset: 1,
								color: '#001D2F'
							}])
						}
					},
					data:[20, 50, 60,120, 170, 100, 50]
				}
			]
		};


		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
		

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer6();
			myChart.resize();
		};
		
		//中下左二
		var worldMapContainer = document.getElementById('box3');
		box03 = document.getElementById("box9");
		box03_h = box03.offsetHeight;
		box03_w = box03.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer = function() {
			worldMapContainer.style.width = box03_w * 0.3 + 'px';
			worldMapContainer.style.height = box03_h * 0.9 + 'px';
			worldMapContainer.style.marginLeft = '2%';
		};
		//设置容器高宽
		resizeWorldMapContainer();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer);

		// 指定图表的配置项和数据
		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b}: {c} ({d}%)"
			},
			grid: {
				height: '100%',
				y: '10%',
				x: '0%',
			},
			title: {
				text:'分区能耗',
				textStyle: {
					color: '#A6CEE2',
					fontStyle:'normal',
					//字体粗细 'normal','bold','bolder','lighter',100 | 200 | 300 | 400...
					fontWeight:'normal',
					//字体系列
					fontFamily:'sans-serif',
					//字体大小
			　　　　fontSize:9
				},
			},
			legend: {
				orient : 'vertical',
				itemWidth: 10,
				itemHeight: 10,
				itemGap: 10,
				x: 'right',
				y: 'top',
				textStyle: {
					fontSize: 12,
					color: '#9FC4E1'
				},
				
				data: res.data.legendList3
			},
			series: [
				{
					name: '分区能耗',
					type: 'pie',
					center: ['35%', '47%'],
					radius: ['40%', '60%'],
					color: ['#d9a503', '#2551bb', '#81b740', '#da70d6', '#ff7f50'],
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList3.length; i++) {
							var item = {
								name: res.data.titleList3[i],
								value: res.data.dataList3[i],
								itemStyle:{
									normal:{
										label:{
											show:false
										},
										labelLine:{
											show:false
										}
									},
									emphasis:{
										labelLine:{
											show:false
										}
									}
								}
								
							};
							serie.push(item);
						}
						return serie;
					}()

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		
		
		
		
		
		
		//中右三
		var worldMapContainer8 = document.getElementById('box33');
		box8_box = document.getElementById("box9");
		box8_box_h = box8_box.offsetHeight;
		box8_box_w = box8_box.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer8 = function() {
			worldMapContainer8.style.width = box8_box_w * 0.33 + 'px';
			worldMapContainer8.style.height = box8_box_h * 0.84 + 'px';
			worldMapContainer8.style.marginLeft = '2%';
		};
		//设置容器高宽
		resizeWorldMapContainer8();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer8);
		// 指定图表的配置项和数据
		var option = {
			grid: {
				left: '5%',
				right: '5%',
				bottom: '15%',
				top: '25%',
				containLabel: true
			},
			title: {
				text:'近七日能耗趋势',
				textStyle: {
					color: '#A6CEE2',
					fontStyle:'normal',
					//字体粗细 'normal','bold','bolder','lighter',100 | 200 | 300 | 400...
					fontWeight:'normal',
					//字体系列
					fontFamily:'sans-serif',
					//字体大小
			　　　　fontSize:9
				},
			},
			xAxis : [
				{
					type : 'category',
					axisLabel:{
						rotate:30,
						textStyle:{
							 color:"#FEFFFF",
							 fontSize:9
						},
						interval:0
					},
					boundaryGap : false,
					axisTick:false,
					axisLine:{
							lineStyle:{
								color:'#0E4892',
								width:1,
							}
					},
					splitLine:{
							show:false,
							lineStyle:{
								color:['#1551B1'],
								width:1,
								type:"dashed"
							}
					},
					data : ['10/11','10/12','10/13','10/14','10/15','10/16']
				}
			],
			yAxis : [
				{
					axisLabel:{
						textStyle:{
							 color:"#0087A7"
						}
					},
					axisLine: {
						show: false
					},
					splitLine:{
							lineStyle:{
								color:['#053A62'],
								width:1,
							}
					},
					axisTick:false,
					type : 'value'
				}
			],
			series : [
				{
					type:'line',
					stack: '总量',
					label: {
						normal: {
							show: true,
							position: 'top'
						}
					}, 
					smooth: false,
					symbol: 'emptyCircle',
					symbolSize: 5,
					sampling: 'average',
					itemStyle: {
						normal: {
							color: '#FFD801'
						}
					},
					stack: 'a',
					data:[100, 110, 140,120, 90, 100]
				}
			]
		};


		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
		
		
		
		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer();
			myChart.resize();
		};
		//资产占比和资金占比---2
		var worldMapContainer = document.getElementById('boxes3');
		box03 = document.getElementById("box03");
		box03_h = box03.offsetHeight;
		box03_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer = function() {
			worldMapContainer.style.width = box03_w * 1 + 'px';
			worldMapContainer.style.height = box03_h * 0.8 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer);

		// 指定图表的配置项和数据
		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b}: {c} ({d}%)"
			},
			grid: {
				height: '40%',
				y: '5%',
				x: '14%'
			},
			legend: {
				x: 'center',
				y: 'bottom',
				textStyle: {
					color: '#ccc'
				},
				data: res.data.legendList3
			},
			series: [{
					color: ['#7627cb', '#259fd2', '#e26021', '#c7353a', '#f5b91e'],
					name: '资金占比',
					type: 'pie',
					selectedMode: 'single',
					radius: '40%',
					center: ['50%', '40%'],

					label: {
						normal: {
							position: 'inner'
						}
					},
					labelLine: {
						normal: {
							show: true
						}
					},
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList4.length; i++) {
							var item = {
								name: res.data.titleList4[i],
								value: res.data.dataList42[i]
							};
							serie.push(item);
						}
						return serie;
					}()

				},
				{
					name: '资产占比',
					type: 'pie',
					center: ['50%', '40%'],
					radius: ['50%', '65%'],
					color: ['#d9a503', '#2551bb', '#81b740', '#da70d6', '#ff7f50'],
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList3.length; i++) {
							var item = {
								name: res.data.titleList3[i],
								value: res.data.dataList32[i]
							};
							serie.push(item);
						}
						return serie;
					}()

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer();
			myChart.resize();
		};
		//左二
		var worldMapContainer5 = document.getElementById('box5');
		box04 = document.getElementById("box04");
		box04_h = box04.offsetHeight;
		box04_w = box04.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer5 = function() {
			worldMapContainer5.style.width = box04_w * 0.96 + 'px';
			worldMapContainer5.style.height = box04_h * 0.9 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer5();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer5);

		// 指定图表的配置项和数据
		option = {
			tooltip : {
				formatter: "{a} <br/>{c} {b}"
			},
			toolbox: {
				show: false
			},
			series : [
				{
					name: '电',
					type: 'gauge',
					z: 3,
					min: 0,
					max: 100,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					radius: '80%',
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 12,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length: -2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						backgroundColor: 'auto',
						borderRadius: 2,
						color: '#eee',
						padding: 3,
						textShadowBlur: 2,
						textShadowOffsetX: 1,
						textShadowOffsetY: 1,
						textShadowColor: '#222',
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '100' : return '100%';
							}
						}
					},
					title : {
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'{value}%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12
						}
					},
					data:[{value: 35, name: '电'}]
				},
				{
					name: '水',
					type: 'gauge',
					center: ['15%', '50%'],    // 默认全局居中
					radius: '60%',
					min:0,
					max:100,
					startAngle:180,
					endAngle:0,
					splitNumber:5,
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 10,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						length:-2,        // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					splitLine: {           // 分隔线
						length:0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						formatter:function(v){
							switch (v + '') {
								case '0' :  return '0%';
								case '100' : return '100%';
							}
						}
						
					},
					pointer: {
						width:5
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'{value}%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
						}
					},
					data:[{value: 82, name: '水'}]
				},
				{
					name: '气',
					type: 'gauge',
					center: ['85%', '50%'],    // 默认全局居中
					radius: '60%',
					min: 0,
					max: 100,
					startAngle:180,
					endAngle:0,
					splitNumber: 5,
					axisLine: {            // 坐标轴线
						lineStyle: {       // 属性lineStyle控制线条样式
							width: 10,
							color: [[0.2, '#81b740'],[0.8, '#E5BD66'],[1, '#B5425A']],
						}
					},
					axisTick: {            // 坐标轴小标记
						splitNumber: 5,
						length: -2,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle控制线条样式
							color: 'auto'
						}
					},
					axisLabel: {
						formatter:function(v){
							switch (v + '') {
								case '0' : return '0%';
								case '100' : return '100%';
							}
						}
					},
					splitLine: {           // 分隔线
						length: 0,         // 属性length控制线长
						lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
							color: 'auto'
						}
					},
					pointer: {
						width:5
					},
					title : {
						// 其余属性默认使用全局文本样式，详见TEXTSTYLE
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
							color:"#56C7E9"
						}
					},
					detail: {
						formatter:'{value}%',
						textStyle: {
							fontWeight: 'bold',
							fontSize: 12,
						}
					},
					data:[{value: 55, name: '气'}]
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer5();
			myChart.resize();
		};
		//业务数据分布
		var worldMapContainer1 = document.getElementById('box1');
		box02 = document.getElementById("box02");
		box02_h = box02.offsetHeight;
		box02_w = box02.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer1 = function() {
			worldMapContainer1.style.width = box02_w * 0.9 + 'px';
			worldMapContainer1.style.height = box02_h * 0.82 + 'px';
		};
		//设置容器高宽
		resizeWorldMapContainer1();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer1);
		// 指定图表的配置项和数据
		function randomData() {
			return Math.round(Math.random() * 3000);
		}
		var option = {
			tooltip: {
				trigger: 'item'
			},
			legend: {
				orient: 'vertical',
				x: 'left',
				y: 'bottom',
				data: [
					'数据1',
					'数据2',
					'数据3'
				],
				textStyle: {
					color: '#ccc'
				}
			},
			visualMap: {
				min: 0,
				max: 2500,
				left: 'right',
				top: 'bottom',
				text: ['高', '低'], // 文本，默认为数值文本
				calculable: true,
				//		color: ['#26cfe4', '#f2b600', '#ec5845'],
				textStyle: {
					color: '#fff'
				}
			},
			series: [{
					name: '数据1',
					type: 'map',
					aspectScale: 0.75,
					zoom: 1.2,
					mapType: 'china',
					roam: false,
					label: {
						normal: {
							show: false
						},
						emphasis: {
							show: false
						}
					},
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList7.length; i++) {
							var item = {
								name: res.data.titleList7[i],
								value: randomData()
							};
							serie.push(item);
						}
						return serie;
					}()

				},
				{
					name: '数据2',
					type: 'map',
					mapType: 'china',
					label: {
						normal: {
							show: true
						},
						emphasis: {
							show: true
						}
					},
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList8.length; i++) {
							var item = {
								name: res.data.titleList8[i],
								value: randomData()
							};
							serie.push(item);
						}
						return serie;
					}()

				},
				{
					name: '数据3',
					type: 'map',
					mapType: 'china',
					label: {
						normal: {
							show: true
						},
						emphasis: {
							show: true
						}
					},
					data: function() {
						var serie = [];
						for(var i = 0; i < res.data.titleList9.length; i++) {
							var item = {
								name: res.data.titleList9[i],
								value: randomData()
							};
							serie.push(item);
						}
						return serie;
					}()

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);

		//用于使chart自适应高度和宽度
		window.onresize = function() {
			//重置容器高宽
			resizeWorldMapContainer1();
			myChart.resize();
		};
	});
});