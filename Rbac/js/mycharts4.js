if($(window).width() > 991) {
	var total = window.innerHeight;
	document.getElementById("title").style.height = total * 0.1 + "px";
	document.getElementById("main").style.height = total * 0.9 + "px";
	title = document.getElementById("title");
	main = document.getElementById("main");
    box01 = document.getElementById("box01");
	box02 = document.getElementById("box02");	
	box03 = document.getElementById("box03");
	box04 = document.getElementById("box04");
	box05 = document.getElementById("box05");
	box06 = document.getElementById("box06");
	title_h = title.offsetHeight;
	main_h = main.offsetHeight;

	box02_h = box02.offsetHeight;

	box01.style.height = main_h * 0.3333 + "px";
	box02.style.height = main_h * 0.3333 + "px";
	box03.style.height = main_h * 0.3333 + "px";
	box04.style.height = main_h * 0.50 + "px";	
	box05.style.height = main_h * 0.50-16 + "px";
	box06.style.height = main_h * 0.50-16 + "px";
	box01_h = box01.offsetHeight;
	document.getElementById("total-mn1").style.height = box01_h * 0.02 + "px";
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
		
		//上左					
		var worldMapContainer = document.getElementById('box1');
		box01 = document.getElementById("box01");
		box01_h = box01.offsetHeight;
		box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer = function() {
			worldMapContainer.style.width = box01_w * 0.9 + 'px';
			worldMapContainer.style.height = box01_h * 0.9 + 'px';
			worldMapContainer.style.marginTop = '10px';
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
			legend: {
				orient : 'vertical',
				x: '60%',
				y: '45%',
				itemWidth: 15,
				itemHeight: 15,
				data:['新用户 15','老用户 6'],
				textStyle: {
							fontSize: 12,
							color: '#88B4CD',					
						},
			},
			series: [
				{
					name:'',
					type:'pie',
					selectedMode: 'single',
					center: ['30%', '55%'],
					radius: [0, '70%'],
					color: ['#13CB6E', '#248CFD'],
					label: {
						normal: {
							position: 'inner'
						}
					},
					labelLine: {
						normal: {
							show: false
						}
					},
					data:[
						{value:15, name:'新用户 15', selected:true},
						{value:6, name:'老用户 6'},
					],
					itemStyle:{ 
						normal:{ 
						  label:{ 
							show: true, 
							formatter: '{c}' 
							}, 
						  labelLine :{show:true} 
						  } 
					} 
				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
		
		var worldMapContainer = document.getElementById('box3');
		box03 = document.getElementById("box03");
		box03_h = box03.offsetHeight;
		box03_w = box03.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer = function() {
			worldMapContainer.style.width = box03_w * 0.9 + 'px';
			worldMapContainer.style.height = box03_h * 0.9 + 'px';
			worldMapContainer.style.marginTop = '10px';
		};
		//设置容器高宽
		resizeWorldMapContainer();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer);

		// 指定图表的配置项和数据
		var option = {
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b}:{c} ({d}%)"
			},
			grid: {
				height: '100%',
				y: '10%',
				x: '0%'
			},
			legend: {
				orient : 'vertical',
				itemWidth: 15,
				itemHeight: 15,
				x: '60%',
				y: '20%',
				textStyle: {
					fontSize: 12,
					color: '#88B4CD',
				},
				data: ["民宿收藏 6","农田收藏 5","农产品收藏 4","软装收藏 7"],
			},
			series: [
				{
					name: '',
					type: 'pie',
					center: ['30%', '55%'],
					radius: ['40%', '70%'],
					color: ['#3642f6', '#03d7e0', '#00b4fa','#b6b447'],
					label: {
						normal: {
							formatter: ""
						}
					},
					labelLine: {
						normal: {
							show: false,
							length:30
						}
					},
					data:[
						{value:6, name:'民宿收藏 6'},
						{value:5, name:'农田收藏 5'},
						{value:4, name:'农产品收藏 4'},
						{value:7, name:'软装收藏 7'}						
					]

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		var worldMapContainer2 = document.getElementById('box4');
		var box01 = document.getElementById("box04");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			// worldMapContainer2.style.width = box01_w * 0.95 + 'px';
			worldMapContainer2.style.height = box01_h * 0.9 + 'px';
			worldMapContainer2.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer2();
		
		
		var worldMapContainer44 = document.getElementById('box44');
		var box44 = document.getElementById("box04");
		var box44_h = box44.offsetHeight;
		var box44_w = box44.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer44 = function() {
			// worldMapContainer2.style.width = box01_w * 0.95 + 'px';
			worldMapContainer44.style.height = box44_h * 0.9 + 'px';
			worldMapContainer44.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer44();
		var worldMapContainer444 = document.getElementById('box444');
		var box444 = document.getElementById("box04");
		var box444_h = box444.offsetHeight;
		var box444_w = box444.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer444 = function() {
			// worldMapContainer2.style.width = box01_w * 0.95 + 'px';
			worldMapContainer444.style.height = box444_h * 0.9 + 'px';
			worldMapContainer444.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer444();
		var worldMapContainer4444 = document.getElementById('box4444');
		var box4444 = document.getElementById("box04");
		var box4444_h = box4444.offsetHeight;
		var box4444_w = box4444.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer4444 = function() {
			// worldMapContainer2.style.width = box01_w * 0.95 + 'px';
			worldMapContainer4444.style.height = box4444_h * 0.9 + 'px';
			worldMapContainer4444.style.float = "center";
		};
		resizeWorldMapContainer4444();
		
		
		
		
		
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer2);
		var option = {			
			color: ['#FCDA1E'],
			tooltip: {
				trigger: 'axis',
				formatter: "{b}<br>{a} : {c}",
				axisPointer: { // 坐标轴指示器，坐标轴触发有效
					type: '' // 默认为直线，可选为：'line' | 'shadow'
				}
			},
			
			textStyle: {
				color: '#ccc'
			},
			grid: {
				top: '10%',
				left: '3%',
				right: '0%',
				bottom: '6%',
				containLabel: true
			},
			xAxis: [{
				type: 'category',
				data: ["0:00","1:00","2:00","3:00","4:00","5:00","6:00","7:00","8:00","9:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","24:00"],
				axisTick:true,
				axisLine:{
                        lineStyle:{
                            color:'#0E4892',
                            width:1,//这里是为了突出显示加上的
                        }
                },
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
				type: 'value'
			}],
			series: [{
				name: '报警数',
				type: 'line',
				symbol: 'circle',
                symbolSize: 12,
				smooth:true,
				barWidth: '30%',				
				data: ["12","4","1","9","9","1","8","2","5","2","4","3","16","10","18","7","15","1","5","16","4","1","13","28","20"],				
			}]
		};
		myChart.setOption(option);
		
		var worldMapContainer2 = document.getElementById('box5');
		var box01 = document.getElementById("box05");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			worldMapContainer2.style.width = box01_w * 0.9 + 'px';
			worldMapContainer2.style.height = box01_h * 0.75 + 'px';
			worldMapContainer2.style.float = "center";
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
			textStyle: {
				color: '#ccc'
			},
			grid: {
				top: '10%',
				left: '3%',
				right: '3%',
				bottom: '6%',
				containLabel: true
			},
			legend: {
				data: ['下单量'],
				align: 'right',
				right:'0',
				textStyle:[{
					color:'#FCDA1E'
				}],
			},
			xAxis: [{
				type: 'category',
				data: ["周一","周二","周三","周四","周五","周六","周日"],
				axisTick:false,
				axisLine:{
                        lineStyle:{
                            color:'#0E4892',
                            width:1,//这里是为了突出显示加上的
                        }
                },
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
				type: 'value'
			}],
			series: [{
				name: '次数',
				type: 'bar',
				barWidth: '30%',
				
				data: ["12","9","7","6","6","4","3","2","2"],
			}]
		};
		myChart.setOption(option);
			
	});
});