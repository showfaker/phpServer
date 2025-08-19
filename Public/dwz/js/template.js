	function splitanddivide()
	{
		flag=1;
		$("#template").find("input").each(function()//[name='p']
		{	  
			var value=this.value.split(",");
			if(value.length>=2)
			{	
				if(flag==1)
				{
					value1=value;
					thisobj=this;
					ids=$(this).attr("alts");
					length=value.length;
				}
				if(flag==2)value2=value;if(flag==3)value3=value;if(flag==4)value4=value;if(flag==5)value5=value;if(flag==6)value6=value;
				if(flag==7)value7=value;if(flag==8)value8=value;if(flag==9)value9=value;if(flag==10)value10=value;
				flag=parseInt(flag)+parseInt("1");
				$(this).val(value[0]);
			}
		})
		for(i=length-1;i>=1;i--)
		{
			//var index=ids.lastIndexOf('_');
			//var v_space=ids.substring(0,index);
			var v_space=ids;
			var v_item=ids+"_"+item;
			var v_name=ids.replace("p","g")+"_"+item;
			var v_item0=ids.replace("p","e")+"_"+item;
			var v_item1=ids.replace("p","e")+"_"+item+"-1";
			var v_item2=ids.replace("p","e")+"_"+item+"-2";
			var v_item3=ids.replace("p","e")+"_"+item+"-3";
			var v_item4=ids.replace("p","e")+"_"+item+"-4";
			var v_item5=ids.replace("p","e")+"_"+item+"-5";
			var v_item6=ids.replace("p","e")+"_"+item+"-6";
			var v_item7=ids.replace("p","e")+"_"+item+"-7";
			var v_item8=ids.replace("p","e")+"_"+item+"-8";
			var v_item9=ids.replace("p","e")+"_"+item+"-9";
			var v_item10=ids.replace("p","e")+"_"+item+"-10";
			var g=ids.replace("p","g")+"_"+item;
			
			//$(thisobj.parentNode.parentNode).after('<tr space='+v_space+' area='+v_area+' item='+v_item+'> 		 			<td class="normaltemplate"><input sort="g" '+g+' value="" style="text-align:center"></td> 			<td class="normaltemplate" width="10%"> 		 			<img src="__PUBLIC__/dwz/images/delete3.png" style="float:left" onclick="DeleteTr3(this)"> 		 			<input type="text" name='+v_item1+' style="width:150px" value=""></td> 			 			<td class="normaltemplate" width="35%"><input type="text" name='+v_item2+' value="" style="width:240px" ></td> 		 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item3+' value="" style="width:25px"></td> 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item4+' value="" style="width:35px"></td> 		 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item5+' value=""></td> 		 			<td class="normaltemplate" width="6%"> <input type="text" name='+v_item6+' value=""></td> 		 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item7+' value=""></td> 	 			<td class="normaltemplate" width="6%"> <input type="text" name='+v_item8+' value=""></td> 	 			<td class="normaltemplate" width="6%"> <input type="text" name='+v_item9+' alt='+v_space+' value="" style="width:50px" onkeyup="counttotal(this.alt)"></td> 	 			<td class="normaltemplate" width="8%"><input type="text" name='+v_item10+' value="" style="width:100px"></td> 			<td class="normaltemplate" width="2%"><img src="__PUBLIC__/dwz/images/add3.png" alt='+v_space+' onclick="onAddTR3(this.alt,this.parentNode.parentNode)"></td> 			</tr> 	 			');
			$(thisobj.parentNode.parentNode).after('<tr space='+v_space+' item='+v_item+'> 			<td class="normaltemplate"><input name='+v_name+' sort="g" value="" style="text-align:center"></td> 	 			<td class="normaltemplate" width="10%"> 		 		 			<img src="__PUBLIC__/dwz/images/delete3.png" style="float:left" onclick="DeleteTr3(this)"> 		 			 			<input type="text" name='+v_item1+' style="width:111px" alts='+v_space+' value=""><a class="btnLook" href="__URL__/lookup" lookupGroup='+v_item0+'>查找带回</a></td> 			  			<td class="normaltemplate" width="35%"><input type="text" name='+v_item2+' value="" style="width:240px" ></td> 		  			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item3+' value="" style="width:25px;"></td> 		 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item4+' value="" style="width:35px" onkeyup="counttotalsub(this.name)"></td> 		  			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item5+' value="" onkeyup="counttotalsub(this.name)"></td> 		 	 			<td class="normaltemplate" width="6%"> <input type="text" name='+v_item6+' value="" onkeyup="counttotalsub(this.name)"></td> 		 	 			<td class="normaltemplate" width="3%"> <input type="text" name='+v_item7+' value="" onkeyup="counttotalsub(this.name)"></td> 	 	 			<td class="normalgray" width="6%"> <input type="text" name='+v_item8+' value="" readonly style="background-color:#F7F7F7"></td> 	 	 			<td class="normalgray" width="6%"> <input type="text" name='+v_item9+' math='+v_space+' value="" style="width:50px;background-color:#F7F7F7" readonly></td> 	  			<td class="normaltemplate" width="8%"><input type="text" name='+v_item10+' value="" style="width:100px"></td> 			 			<td class="normaltemplate" width="2%"><img src="__PUBLIC__/dwz/images/add3.png" alt='+v_space+' onclick="onAddTR3(this.alt,this.parentNode.parentNode)"></td> 		 			</tr> 	 			');
			initUI($("#template"));
			
			document.getElementsByName(v_item1)[0].value=value1[i];
			document.getElementsByName(v_item2)[0].value=value2[i];
			document.getElementsByName(v_item3)[0].value=value3[i];
			document.getElementsByName(v_item4)[0].value=value4[i];
			document.getElementsByName(v_item5)[0].value=value5[i];
			document.getElementsByName(v_item6)[0].value=value6[i];
			document.getElementsByName(v_item7)[0].value=value7[i];
			document.getElementsByName(v_item8)[0].value=value8[i];
			document.getElementsByName(v_item9)[0].value=value9[i];
			document.getElementsByName(v_item10)[0].value=value10[i];
			item++;
		}

		key=1;
		$("#template").find("input[sort='g']").each(function()
		{
				this.value=key;
				key=key+1;
		})	
		/*计算总值*/
		ids=ids.replace("e","p");

		var sum=0;var all=0;
		$("#template").find("input[math='"+ids+"']").each(function()
		{
			sum+=Number(this.value);
		})
		var v_name=ids.replace("p","total");
		document.getElementsByName(v_name)[0].value=sum.toFixed(1);
		
		$("#template").find("input[math='total']").each(function()
		{
			all+=Number(this.value);
		})
		document.getElementsByName("all")[0].value=all.toFixed(2);
		
		
	}