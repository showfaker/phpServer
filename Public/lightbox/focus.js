$(function() {
			var sWidth = $("#focus").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus").append(btn);
			$("#focus .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus1").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus1 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus1").append(btn);
			$("#focus1 .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus1 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus1 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus1 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus1").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus1 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus1 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus1 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus2").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus2 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus2").append(btn);
			$("#focus2 .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus2 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus2 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus2 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus2").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus2 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus2 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus2 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus3").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus3 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus3").append(btn);
			$("#focus3 .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus3 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus3 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus3 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus3").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus3 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus3 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus3 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus4").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus4 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus4").append(btn);
			$("#focus4 .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus4 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus4 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus4 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus4").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus4 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus4 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus4 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		
		$(function() {
			var sWidth = $("#focus5").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus5 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus5").append(btn);
			$("#focus5 .btnBg").css("opacity",0.5);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus5 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus5 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus5 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus5").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus5 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus5 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus5 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		
		
		$(function() {
			var sWidth = $("#focus11").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus11 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus11").append(btn);
			$("#focus11 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus11 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus11 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus11 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus11").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus11 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus11 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus11 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		
		$(function() {
			var sWidth = $("#focus12").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus12 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus12").append(btn);
			$("#focus12 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus12 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus12 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus12 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus12").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus12 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus12 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus12 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		
		$(function() {
			var sWidth = $("#focus13").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus13 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus13").append(btn);
			$("#focus13 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus13 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus13 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus13 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus13").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus13 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus13 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus13 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		$(function() {
			var sWidth = $("#focus14").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus14 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus14").append(btn);
			$("#focus14 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus14 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus14 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus14 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus14").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus14 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus14 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus14 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		$(function() {
			var sWidth = $("#focus15").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus15 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus15").append(btn);
			$("#focus15 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus15 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus15 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus15 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus15").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus15 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus15 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus15 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		$(function() {
			var sWidth = $("#focus16").width(); //��ȡ����ͼ�Ŀ�ȣ���ʾ�����
			var len = $("#focus16 ul li").length; //��ȡ����ͼ����
			var index = 0;
			var picTimer;
			
			//���´���������ְ�ť�Ͱ�ť��İ�͸������������һҳ����һҳ������ť
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			
			$("#focus16").append(btn);
			$("#focus16 .btnBg").css("opacity",0);
		
			//ΪС��ť�����껬���¼�������ʾ��Ӧ������
			$("#focus16 .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus16 .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");
		
			//����Ϊ���ҹ�����������liԪ�ض�����ͬһ�����󸡶�������������Ҫ�������ΧulԪ�صĿ��
			$("#focus16 ul").css("width",sWidth * (len));
			
			//��껬�Ͻ���ͼʱֹͣ�Զ����ţ�����ʱ��ʼ�Զ�����
			$("#focus16").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //��4000�����Զ����ŵļ������λ������
			}).trigger("mouseleave");
			
			//��ʾͼƬ���������ݽ��յ�indexֵ��ʾ��Ӧ������
			function showPics(index) { //��ͨ�л�
				var nowLeft = -index*sWidth; //����indexֵ����ulԪ�ص�leftֵ
				$("#focus16 ul").stop(true,false).animate({"left":nowLeft},300); //ͨ��animate()����ulԪ�ع������������position
				//$("#focus16 .btn span").removeClass("on").eq(index).addClass("on"); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
				$("#focus16 .btn span").stop(true,false).animate({"opacity":"0.4"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //Ϊ��ǰ�İ�ť�л���ѡ�е�Ч��
			}
		});
		
		