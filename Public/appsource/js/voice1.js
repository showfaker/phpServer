var gentry = null,
	hl = null,
	le = null;
var er = null,
	ep = null;
var bUpdated = false; //用于兼容可能提前注入导致DOM未解析完更新的问题
function outLine(msg) {
	$('#output').text(msg);
}
// H5 plus事件处理
function plusReady() {
	// 获取音频目录对象
	plus.io.resolveLocalFileSystemURL('_doc/', function(entry) {
		entry.getDirectory('audio', {
			create: true
		}, function(dir) {
			gentry = dir;
			updateHistory();
		}, function(e) {
			outLine('Get directory "audio" failed: ' + e.message);
		});
	}, function(e) {
		outLine('Resolve "_doc/" failed: ' + e.message);
	});
}
if(window.plus) {
	plusReady();
} else {
	document.addEventListener('plusready', plusReady, false);
}

// DOMContentLoaded事件处理
document.addEventListener('DOMContentLoaded', function() {
	// 获取DOM元素对象
	hl = document.getElementById('history');
	le = document.getElementById('empty');
	er = document.getElementById('record');
	rt = document.getElementById('rtime');
	ep = document.getElementById('play');
	pt = document.getElementById('ptime');
	pp = document.getElementById('progress')
	ps = document.getElementById('schedule');
	updateHistory();
}, false);

// 开始录音
var r = null,
	t = 0,
	ri = null,
	rt = null;

function startRecord() {
	//outSet('开始录音：');
	console.log('开始录音：');
	r = plus.audio.getRecorder();
	if(r == null) {
		console.log('录音对象未获取')
		outLine('录音对象未获取');
		return;
	}
	r.record({
		filename: '_doc/audio/'
		//format: '3gp'
	}, function(p) {
		console.log('录音完成：' + p)
		outLine('录音完成：' + p);
		plus.io.resolveLocalFileSystemURL(p, function(entry) {
			//createItem(entry);
			addVoice(p, entry);
		}, function(e) {
			outLine('读取录音文件错误：' + e.message);
		});

	}, function(e) {
		outLine('录音失败：' + e.message);
	});
	er.style.display = 'block';
	t = 0;
	ri = setInterval(function() {
		t++;
		rt.innerText = timeToStr(t);
	}, 1000);
} // 停止录音
function stopRecord() {
	er.style.display = 'none';
	rt.innerText = '00:00:00';
	clearInterval(ri);
	ri = null;
	r.stop();
	w = null;
	r = null;
	t = 0;
}

// 清除历史记录
function cleanHistory() {
	//hl.innerHTML = '<li id="empty" class="ditem-empty">无历史记录</li>';
	//le = document.getElementById('empty');
	// 删除音频文件
	outLine('清空录音历史记录：');
	gentry.removeRecursively(function() {
		// Success
		console.log('清空录音历史记录，操作成功！');
		outLine('清空录音历史记录，操作成功！');
	}, function(e) {
		console.log('清空录音历史记录，操作失败：' + e.message);
		outline('清空录音历史记录，操作失败：' + e.message);
	});
}

// 获取录音历史列表
function updateHistory() {
	if(bUpdated || !gentry || !document.body) { //兼容可能提前注入导致DOM未解析完更新的问题
		return;
	}
	var reader = gentry.createReader();
	reader.readEntries(function(entries) {
		for(var i in entries) {
			if(entries[i].isFile) {
				createItem(entries[i]);
			}
		}
	}, function(e) {
		outLine('读取录音列表失败：' + e.message);
	});
	bUpdated = true;
}

timeToStr = function(ts) {
	if(isNaN(ts)) {
		return "--:--:--";
	}
	var h = parseInt(ts / 3600);
	var m = parseInt((ts % 3600) / 60);
	var s = parseInt(ts % 60);
	return(ultZeroize(h) + ":" + ultZeroize(m) + ":" + ultZeroize(s));
};

ultZeroize = function(v, l) {
	var z = "";
	l = l || 2;
	v = String(v);
	for(var i = 0; i < l - v.length; i++) {
		z += "0";
	}
	return z + v;
};

function addVoice(path, entry) {
	console.log("entry = " + entry.name)
	console.log("上传前的文件名：" + entry.name);
	uploadVoice(path);
}

function uploadVoice(path) {
	console.log("开始上传：")
	var uploadUrl = "http://192.168.9.105:8860/v1/uploadDownload/uploadFile";
	var downUrl = "http://192.168.9.105:8860/v1/uploadDownload/downloadFile";
	var task = plus.uploader.createUpload(uploadUrl, {
			method: "POST"
		},
		function(t, status) { //上传完成
			if(status == 200) {
				var data = JSON.parse(t.responseText);
				console.log("上传成功,返回文件名为 ：" + data.data);
				outLine("文件上传成功：" + t.responseText);
				var v = $("<audio controls='controls'/>");
				v.attr("src", downUrl + "?imageName=" + data.data);
				$("#v1").append(v);
				//上传下载成功之后要清除本地的录音文件
				cleanHistory();
				//startPlay(downUrl + "?imageName=" + data.data);
				/*plus.storage.setItem("uploader", t.responseText);
				var w = plus.webview.create("uploader_ret.html", "uploader_ret.html", {
					scrollIndicator: 'none',
					scalable: false
				});*/
				/*w.addEventListener("loaded", function() {
					//wt.close();
					w.show("slide-in-right", 300);
				}, false);*/
			} else {
				outLine("上传失败：" + status);
				//wt.close();
			}
		}
	);
	task.addData("client", "HelloH5+");
	task.addData("uid", getUid());
	/*for(var i = 0; i < files.length; i++) {
		var f = files[i];
		task.addFile(f.path, {
			key: f.name
		});
	}*/
	task.addFile(path, {
		key: "file"
	})
	task.start();
}

// 产生一个随机数
function getUid() {
	return Math.floor(Math.random() * 100000000 + 10000000).toString();
}

// 播放文件相关对象
var p = null,
	pt = null,
	pp = null,
	ps = null,
	pi = null;

function startPlay(url) {
	//ep.style.display = 'block';
	//var L = pp.clientWidth;
	p = plus.audio.createPlayer(url);
	p.play(function() {
		outLine('播放完成！');
		// 播放完成
		/*pt.innerText = timeToStr(d) + '/' + timeToStr(d);
		ps.style.webkitTransition = 'all 0.3s linear';
		ps.style.width = L + 'px';
		stopPlay();*/
	}, function(e) {
		outLine('播放音频文件"' + url + '"失败：' + e.message);
	});
	// 获取总时长
	/*var d = p.getDuration();
	if(!d) {
		pt.innerText = '00:00:00/' + timeToStr(d);
	}*/
	/*pi = setInterval(function() {
		if(!d) { // 兼容无法及时获取总时长的情况
			d = p.getDuration();
		}
		var c = p.getPosition();
		if(!c) { // 兼容无法及时获取当前播放位置的情况
			return;
		}
		pt.innerText = timeToStr(c) + '/' + timeToStr(d);
		var pct = Math.round(L * c / d);
		if(pct < 8) {
			pct = 8;
		}
		ps.style.width = pct + 'px';
	}, 1000);*/
}

// 停止播放
function stopPlay() {
	clearInterval(pi);
	pi = null;
	setTimeout(resetPlay, 500);
	// 操作播放对象
	if(p) {
		p.stop();
		p = null;
	}
}

// 重置播放页面内容
function resetPlay() {
	ep.style.display = 'none';
	ps.style.width = '8px';
	ps.style.webkitTransition = 'all 1s linear';
	pt.innerText = '00:00:00/00:00:00';
}

// 拍照
function getImage() {
	outLine('开始拍照：');
	var cmr = plus.camera.getCamera();
	cmr.captureImage(function(p) {
		outLine('拍照成功：' + p);
		plus.io.resolveLocalFileSystemURL(p, function(entry) {
			//createItem(entry);
			uploadPhoto(p, entry);
		}, function(e) {
			outLine('读取拍照文件错误：' + e.message);
		});
	}, function(e) {
		outLine('失败：' + e.message);
	}, {
		filename: '_doc/camera/',
		index: 1
	});
}
// 录像
function getVideo() {
	outLine('开始录像：');
	var cmr = plus.camera.getCamera();
	cmr.startVideoCapture(function(p) {
		outLine('录像成功：' + p);
		plus.io.resolveLocalFileSystemURL(p, function(entry) {
			//createItem(entry);
			uploadVideo(p, entry)
		}, function(e) {
			outLine('读取录像文件错误：' + e.message);
		});
	}, function(e) {
		outLine('失败：' + e.message);
	}, {
		filename: '_doc/camera/',
		index: i
	});
}