function requestFormCheck(varForm)
{
   if(typeof(varForm.parameters) != "undefined"){
		if(varForm.parameters.value == "")
		{
			alert('请填写参数');
			varForm.parameters.focus();
			return false;
		}
	}
	if(typeof(varForm.usage) != "undefined"){
		if(varForm.usage.value == "")
		{
			alert('请填写用途');
			varForm.usage.focus();
			return false;
		}
	}
	
	if(typeof(varForm.receiptNum) != "undefined"){
		if(varForm.receiptNum.value == "")
		{
			alert('请填写单据张数');
			varForm.receiptNum.focus();
			return false;
		}
		else if(isNaN(varForm.receiptNum.value))
		{
			alert('单据张数必须为数字');
			varForm.receiptNum.focus();
			return false;
		}
	}
	if(typeof(varForm.amount) != "undefined"){
		if(varForm.amount.value == "")
		{
			alert('请填写报销金额');
			varForm.amount.focus();
			return false;
		}
		else if(isNaN(varForm.amount.value))
		{
			alert('报销金额必须为数字');
			varForm.amount.focus();
			return false;
		}
	}
	
	if(typeof(varForm.MaterialItemCnt) != "undefined")
	{
		var itemCnt=parseInt(varForm.MaterialItemCnt.value);
		for(var k=1;k<=itemCnt;k++)
		{
			//采购物品名称
			if(typeof('varForm.MaterialName_'+k) != "undefined")
			{
				if(varForm['MaterialName_'+k].value == "")
				{
					alert('请填写物品或服务名称');
					varForm['MaterialName_'+k].focus();
					return false;
				}
			}
			//采购物品数量
			if(typeof('varForm.MaterialCnt_'+k) != "undefined")
			{
				if(varForm['MaterialCnt_'+k].value == "")
				{
					alert('请填写物品数量');
					varForm['MaterialCnt_'+k].focus();
					return false;
				}
				else if(isNaN(varForm['MaterialCnt_'+k].value))
				{
					alert('物品数量必须为数字');
					varForm['MaterialCnt_'+k].focus();
					return false;
				}
			}
			//采购物品单位
			if(typeof('varForm.MaterialUnit_'+k) != "undefined")
			{
				if(varForm['MaterialUnit_'+k].value == "")
				{
					alert('请填写单位');
					varForm['MaterialUnit_'+k].focus();
					return false;
				}
				
			}
			//采购物品单价
			if(typeof('varForm.MaterialUnitprice_'+k) != "undefined")
			{
				if(varForm['MaterialUnitprice_'+k].value == "")
				{
					alert('请填写单价');
					varForm['MaterialUnitprice_'+k].focus();
					return false;
				}
				else if(isNaN(varForm['MaterialUnitprice_'+k].value))
				{
					alert('物品单价必须为数字');
					varForm['MaterialUnitprice_'+k].focus();
					return false;
				}
				
			}
			//采购物品参数
			if(typeof('varForm.MaterialParameter_'+k) != "undefined")
			{
				if(varForm['MaterialParameter_'+k].value == "")
				{
					alert('请填写物品参数');
					varForm['MaterialParameter_'+k].focus();
					return false;
				}
				
			}
		}
		
	}
   

}