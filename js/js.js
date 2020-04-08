//const ajax_file = 'ajaxRequester.php';
//var scroll_on = 0;
//
//window.onload = function()
//{
//	
//}
//
//function ajaxFunction(url,callbackSucces,method='GET',data=null,header=1,czy_edycja=1,asynchro=true,show_progress=0,upload_onprogress=function(){},read_onprogress=function(){},callbackError=function(){alert('Wystąpił błąd.')})
//{
//	if(ajax_on && !asynchro)
//	{
//		let data_to_send = new Array;
//		for(let pair of data.entries()){data_to_send.push(pair[0]);data_to_send.push(pair[1]);}
//		ajax_queue.push([url,callbackSucces,method,data_to_send,header,czy_edycja,asynchro,show_progress,upload_onprogress,read_onprogress,callbackError]);
//		return;
//	}
//	
//	let ajax = new XMLHttpRequest();
//	ajax_on = 1;
//	
//	ajax.onloadstart = function()
//	{
//		cover(0,0);
//	}
//	ajax.onreadystatechange = function()
//	{
//		if(ajax.readyState==4 && ajax.status==200)
//		{
//			ajax_on = 0;
//			callbackSucces(ajax.response);
//			if(ajax_queue[0])
//			{
//				let data_to_send = new FormData();
//				for(let i = 0; i < ajax_queue[0][3].length; i=i+2){data_to_send.append(ajax_queue[0][3][i],ajax_queue[0][3][i+1]);}
//				ajaxFunction(ajax_queue[0][0],ajax_queue[0][1],ajax_queue[0][2],data_to_send,ajax_queue[0][4],ajax_queue[0][5],ajax_queue[0][6],ajax_queue[0][7],ajax_queue[0][8]);
//				ajax_queue.shift();
//			}
//			else if(!asynchro)
//			{
//				document.getElementById('progress').parentElement.removeChild(document.getElementById('progress'));
//				closeWindow();
//			}
//		}
//		//else
//		//	callbackError();
//	}
//	ajax.onloadend = function()
//	{
//		if(czy_edycja) turnOn();
//		deleteCover(1);
//	}
//	
//	//PROGRES - włączamy tylko gdy uploadujemy jakiś plik
//	if(show_progress)
//	{		
//		ajax.upload.onprogress = function(e)
//		{
//			if(e.lengthComputable)
//			{
//				upload_onprogress(e);
//			}
//		}
//		ajax.onprogress = function(e)
//		{
//			if(e.lengthComputable)
//			{
//				read_onprogress();
//			}
//		}
//	}
//	
//	//////////////// ERRORS
//	ajax.onerror = function()
//	{
//		alert('Wystąpił błąd: odśwież stronę i spróbuj ponownie.');
//	}
//	ajax.onabort = function()
//	{
//		//czy jest sens abortować gdzieś zapytanie AJAX jeżeli tak, trzeba użyć metody abort()?
//		alert('Wystąpił błąd: połączenie zostało przerwane.');
//	}
//	ajax.ontimeout = function()
//	{
//		alert('Wystąpił błąd: zbyt długi czas oczekiwania na odpowiedź z serwera.');
//	}
//	
//	ajax.open(method,url,true);
//	if(method=='POST')
//	{
//		if(header===0) ajax.setRequestHeader('Content-type','application/xml');
//		else if(header===1) ajax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
//	}
//	ajax.send(data);	
//}
//
//function cover()
//{
//	let style = 'position:fixed;top:0;left:0;margin:0;padding:0;width:100vw;height:100vh;background-color:rgba(20,20,20,0.7);z-index:100;';
//	createNewElement('div',document.body,style,'','cover');
//}
//
//function deleteCover()
//{
//	try
//	{
//		if(document.getElementById('cover'))
//		{
//			document.body.removeChild(document.getElementById('cover'));
//		}
//	}
//	catch{}
//}
//
//function createNewElement(tag,where=edit_elem,style=null,inner_elem='',id=null,attribute=null)
//{
//	let new_element = document.createElement(tag);
//	if(id !== null)
//		new_element.id = id;
//	if(style !== null)
//		new_element.style = style;
//	if(attribute !== null)
//		new_element.setAttribute(attribute[0],attribute[1]);
//	new_element.innerHTML = inner_elem;
//	//if(tag == 'img')
//	//{
//	//	if(new_element.src == null)
//	//	{
//	//		new_element.src = 'img/fotoblank.jpg';
//	//	}
//	//	if(new_element.alt == null)
//	//	{
//	//		new_element.alt = 'blank';
//	//	}
//	//}
//	if(where == 'return')
//		return new_element;
//	else
//		where.appendChild(new_element);
//}
//
//function changeContentAndUrl(url)
//{	
//	let callback = function(result)
//	{
//		let allHtml = result;
//		let point1 = allHtml.indexOf('<header>');
//		let point2 = allHtml.indexOf('</header>');
//		let header = allHtml.substr(point1,point2-point1+9);
//		point1 = allHtml.indexOf('<main>');
//		point2 = allHtml.indexOf('</main>');
//		let content = allHtml.substr(point1+6,point2-point1-6);
//		point1 = allHtml.indexOf('<title>');
//		point2 = allHtml.indexOf('</title>');
//		let title = allHtml.substr(point1+7,point2-point1-7);
//		point1 = allHtml.indexOf('<meta descrition content="');
//		point2 = allHtml.indexOf('"><title>');
//		let meta_description = allHtml.substr(point1+26,point2-point1-26);
//		document.getElementsByName('description')[0].setAttribute('content',meta_description);
//		document.title = title;
//		document.getElementsByTagName('MAIN')[0].innerHTML = content;
//		window.history.pushState({}, '', '');
//		document.scrollTop = 0;
//		
//		if(url == 'oferta.html' && !scroll_on)
//		{
//			window.addEventListener('scroll',loadMoreMaterials);
//			scroll_on = 1;
//			downloadList();
//		}
//		else if(scroll_on)
//		{
//			window.removeEventListener('scroll',loadMoreMaterials);
//		}
//	}
//	
//	ajaxFunction(ajax_file,callback,'POST');
//}
//
//function loadMoreMaterials()
//{
//	//this.removeEventListener('scroll',loadMoreMaterials);
//	f = document.body.offsetHeight;
//	if(window.scrollY + window.innerHeight > f - 250)
//	{
//		downloadList();
//	}
//	
//	//this.addEventListener('scroll',loadMoreMaterials);
//	return;
//}
//
//function downloadList(new_list = 0) // new_list when filter change
//{
//	let callback = function(res)
//	{
//		try
//		{
//			res = JSON.parse(res);
//			if(res.status == 'OK')
//			{
//				res = res.msg;
//				res = escapeHtml(res);
//				//res = JSON.parse(res);
//			}
//			else
//			{
//				console.log(res);
//				return;
//			}
//		}
//		catch(e){console.log(e)}
//		if(new_list)	
//			document.getElementById('lista_wyszukiwarka').innerHTML = res;
//		else
//			document.getElementById('lista_wyszukiwarka').innerHTML += res;
//	};
//	
//	let data = 'action=getList&key=';
//	if(ajaxObject != null) ajaxObject.abort();
//	ajaxObject = new letsAjax(ajax_file,callback,data,['Content-type', 'application/x-www-form-urlencoded'],'POST');
//}
//
//function escapeHtml(text)
//{
//	return text
//		.replace(/&amp;/g, "&")
//		.replace(/&lt;/g, "<")
//		.replace(/&gt;/g, ">")
//		.replace(/&quot;/g, "\"")
//		.replace(/&#039;/g, "'")
//		.replace(/\//g, "/");
//}

function showContent(i)
{
	if(i.style.height != i.scrollHeight + 'px')
	{
		i.style.height = i.scrollHeight + 'px';
		i.getElementsByClassName('always_bottom')[0].innerHTML = 'kliknij aby zwinąć';
	}
	else
	{
		i.style.height = '200px';
		i.getElementsByClassName('always_bottom')[0].innerHTML = 'kliknij aby rozwinąć';
	}
}