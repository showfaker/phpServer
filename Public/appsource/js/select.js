
function getcity()
{
	mui.ajax("https://project.jiuze9.com/project222/Rbac/index.php/App/getcity/account/"+account+"/webid/"+webid+"/city/"+city,{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型					
		headers:{'Content-Type':'application/json'},	              
		success:function(data){
			var result = new Array();  
			result = eval(data);
			var cityData=[];
			var cityTmp=[];
			for(x in result)   
			{ 
				cityTmp=[];
				var cities = result[x]["cities"];
				for(y in cities)
				{
					var body1 = {
						"value":cities[y]['city'],
						"text":cities[y]['city']
					};
					cityTmp.push(body1);
				}
				var body = {
					"value":result[x]['province'],
					"text":result[x]['province'],
					"children": cityTmp
				};
				cityData.push(body);
			}
			
			var cityPicker = new mui.PopPicker({
				layer: 2
			});
			cityPicker.setData(cityData);
			var showCityPickerButton = document.getElementById('citychoice');
			var citychoice = document.getElementById('citychoice');
			showCityPickerButton.addEventListener('tap', function(event) {
				cityPicker.show(function(items) {
					citychoice.innerText = items[0].text + " " + items[1].text;
					city=items[1].text;
					getlist();
				});
			}, false); 
		},
	});
}
function getuser()
{
	mui.ajax("https://project.jiuze9.com/project222/Rbac/index.php/App/getuser/account/"+account+"/webid/"+webid+"/city/"+city,{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型					
		headers:{'Content-Type':'application/json'},	              
		success:function(data){
			var result = new Array();  
			result = eval(data); 
			var userData=[];
			for(x in result)   
			{  
				var body = {
					"value":result[x]['nickname'],
					"text":result[x]['nickname']
				};
				userData.push(body);
			}
			
			var userPicker = new mui.PopPicker({
				layer: 1
			});
			userPicker.setData(userData);
			var showUserPickerButton = document.getElementById('userchoice');
			var userchoice = document.getElementById('userchoice');
			showUserPickerButton.addEventListener('tap', function(event) {
				userPicker.show(function(items) {
					userchoice.innerText = items[0].text;
					user=items[0].text;
					getlist();
				});
			}, false);
		},
	});
}

function getprojecttype()
{
	mui.ajax("https://project.jiuze9.com/project222/Rbac/index.php/App/getprojecttype/account/"+account+"/webid/"+webid+"/city/"+city,{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型					
		headers:{'Content-Type':'application/json'},	              
		success:function(data){
			var result = new Array();  
			result = eval(data); 
			var projecttypeData=[];
			for(x in result)   
			{  
				var body = {
					"value":result[x]['name'],
					"text":result[x]['name']
				};
				projecttypeData.push(body);
			}
			
			var projecttypePicker = new mui.PopPicker({
				layer: 1
			});
			projecttypePicker.setData(projecttypeData);
			var showUserPickerButton = document.getElementById('projecttypechoice');
			var projecttypechoice = document.getElementById('projecttypechoice');
			showUserPickerButton.addEventListener('tap', function(event) {
				projecttypePicker.show(function(items) {
					projecttypechoice.innerText = items[0].text;
					projecttypecurrentchoice=items[0].text;
					getlist();
				});
			}, false);
		},
	});
}


function getproject()
{
	mui.ajax("https://project.jiuze9.com/project222/Rbac/index.php/App/getproject/account/"+account+"/webid/"+webid+"/city/"+city,{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型					
		headers:{'Content-Type':'application/json'},	              
		success:function(data){
			var result = new Array();  
			result = eval(data); 
			var cityData=[];
			var cityTmp=[];
			for(x in result)   
			{  
				cityTmp=[];
				var cities = result[x]["cities"];
				for(y in cities)
				{
					var body1 = {
						"value":cities[y]['city'],
						"text":cities[y]['city']
					};
					cityTmp.push(body1);
				}
				var body = {
					"value":result[x]['province'],
					"text":result[x]['province'],
					"children": cityTmp
				};
				cityData.push(body);
			}
			
			var cityPicker = new mui.PopPicker({
				layer: 2
			});
			cityPicker.setData(cityData);
			var showCityPickerButton = document.getElementById('citychoice');
			var citychoice = document.getElementById('citychoice');
			showCityPickerButton.addEventListener('tap', function(event) {
				cityPicker.show(function(items) {
					citychoice.innerText = items[1].text;
					city=items[1].text;
					page=1;
					getlist();
				});
			}, false); 
		},
	});
}

function getworktype()
{
	mui.ajax("https://project.jiuze9.com/project222/Rbac/index.php/App/getworktype/account/"+account+"/webid/"+webid+"/city/"+city,{
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型					
		headers:{'Content-Type':'application/json'},	              
		success:function(data){
			var result = new Array();  
			result = eval(data); 
			var userData=[];
			for(x in result)   
			{  
				var body = {
					"value":result[x]['worktype'],
					"text":result[x]['worktype']
				};
				userData.push(body);
			}
			
			var userPicker = new mui.PopPicker({
				layer: 1
			});
			userPicker.setData(userData);
			var showUserPickerButton = document.getElementById('userchoice');
			var userchoice = document.getElementById('userchoice');
			showUserPickerButton.addEventListener('tap', function(event) {
				userPicker.show(function(items) {
					userchoice.innerText = items[0].text;
					worktype=items[0].text;
					page=1;
					getlist();
				});
			}, false);
		},
	});
}