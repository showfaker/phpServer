if($(window).width() > 991) {
	var total = window.innerHeight;
	document.getElementById("title").style.height = total * 0.1 + "px";
	//document.getElementById("title_1").style.height = total * 0.02 + "px";
	document.getElementById("main").style.height = total * 0.85 + "px";
	title = document.getElementById("title");
	main = document.getElementById("main");
    box01 = document.getElementById("box01");
	box02 = document.getElementById("box02");	
	box03 = document.getElementById("box03");
	box04 = document.getElementById("box04");
	title_h = title.offsetHeight;
	main_h = main.offsetHeight;

	box02_h = box02.offsetHeight;
	box01.style.height = main_h * 0.3 + "px";
	box02.style.height = main_h * 0.3 + "px";
    box03.style.height = main_h * 0.39 + "px";
	box04.style.height = main_h * 0.98 + "px";		
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
		var worldMapContainer2 = document.getElementById('box1');
		var box01 = document.getElementById("box01");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			worldMapContainer2.style.width = box01_w * 0.9 + 'px';
			worldMapContainer2.style.height = box01_h * 0.9 + 'px';
			worldMapContainer2.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer2();
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
					color: '#fff'
				},
				grid: {
					top: '20%',
					left: '3%',
					right: '3%',
					bottom: '15%',
					containLabel: true
				},
				xAxis:  {
					type: 'category',
					data: ["0:00","1:00","2:00","3:00","4:00","5:00","6:00","7:00","8:00","9:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","24:00"],
					axisTick:true,
					axisLine:{
						lineStyle:{
							color:'#0E4892',
							width:1,//这里是为了突出显示加上的
						}
					},
					axisLabel:{
						textStyle:{
							 color:"#D8E9EC"
						}
					},
				},
				yAxis: {
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
				},
				series: [
					{
						name:'2018-10-11',
						type:'line',
						symbol: 'star',
						symbolSize: 6,				
						barWidth: '30%',				
						data: ["20","18","16","12","10","8","6","4","2","4","6","8","10","12","14","16","18","20","22","24","26","28","30","26","20"],
					}
				]
			};
		myChart.setOption(option);
        
		var worldMapContainer = document.getElementById('box2');
		box03 = document.getElementById("box02");
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
				y: '0%',
				textStyle: {
					fontSize: 12,
					color: '#88B4CD',
					
					
				},
				data: ["综合楼：100","AF配电间：50","AE/VQ配电间：40","PA配电间：60","PQ配电间：25"],
			},
			series: [
				{
					name: '',
					type: 'pie',
					center: ['30%', '40%'],
					radius: ['40%', '70%'],
					color: ['#3642f6','#00b4fa','#03d7e0','#b6b447','#a93bc2'],
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
						{value:100, name:'综合楼：100'},
						{value:50, name:'AF配电间：50'},
						{value:40, name:'AE/VQ配电间：40'},
						{value:60, name:'PA配电间：60'},
                        {value:25, name:'PQ配电间：25'}						
					]

				}
			]
		};

		// 使用刚指定的配置项和数据显示图表。
		myChart.setOption(option);
		
		
        //上左					
		var worldMapContainer2 = document.getElementById('box3');
		var box01 = document.getElementById("box03");
		var box01_h = box01.offsetHeight;
		var box01_w = box01.offsetWidth;
		//用于使chart自适应高度和宽度,通过窗体高宽计算容器高宽
		var resizeWorldMapContainer2 = function() {
			worldMapContainer2.style.width = box01_w * 0.9 + 'px';
			worldMapContainer2.style.height = box01_h * 0.9 + 'px';
			worldMapContainer2.style.float = "center";
		};
		//设置容器高宽
		resizeWorldMapContainer2();
		// 基于准备好的dom，初始化echarts实例
		var myChart = echarts.init(worldMapContainer2);
		var option = {
			    color:['#4bcdeb'],
				tooltip: {
					trigger: 'axis',
					axisPointer: {
						type: ''
					}
				},
				textStyle: {
					color: '#fff'
				},
				grid: {
					top: '10%',
					left: '2%',
					right: '6%',
					bottom: '3%',
					containLabel: true
				},
				xAxis: {
					 show : false,
				},
				yAxis: {
					axisLine:{
							lineStyle:{
								color:'#0E4892',
								width:1,//这里是为了突出显示加上的
							}
					},
					axisLabel:{
						textStyle:{
							color:"#7EA0BB",
							fontSize:9
						}
					},
					type: 'category',
					data: ['液压配电机','1号消防水泵','2号消防水泵','3号消防水泵','4号消防水泵','5号消防水泵']
				},
				series: [
					{
						name: '',
						type: 'bar',
						barWidth: '60%',
						data: [3, 4, 5, 8, 2, 6],
						itemStyle: {
							normal: {
								label: {
									show: true, //开启显示
									position: 'right', //在上方显示
									textStyle: { //数值样式
										color: '#5893B5',
										fontSize: 12
									}
								}
							}
						},
					}
				]
			};
		myChart.setOption(option);					
	});
});