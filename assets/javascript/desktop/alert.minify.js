define(["jquery","selector"],function(i,t){return{maxShow:5,timeShow:3e3,type:{danger:"danger",success:"success",info:"info",warning:"warning"},arrays:[],init:function(){this.listenShow()},add:function(i,t){t||(t=this.type.danger),this.arrays.push({message:i,type:t}),this.notifyChange()},notifyChange:function(){},listenShow:function(){var i=this;setInterval(function(){if(void 0!==i.arrays){var e=t.alert,a=t.alertList,n=i.arrays.length,s=a.get(0).childElementCount;if(n>0){var h=i.arrays[0],r='<li class="'+h.type+'">';if(r+="<span>"+h.message+"</span>",r+="</li>",s>=i.maxShow||a.get(0).scrollHeight>e.outerHeight()){var o=a.find("li:first-child");o.css({display:"block",width:o.width(),height:o.height()}).animate({opacity:0,marginLeft:e.width()+"px",marginTop:"-"+o.height()+"px"},100,function(){o.remove()})}a.append(r),e.css({display:"block"}),i.arrays.splice(0,1);var l=a.find("li:last-child");l.length&&l.length>0&&l.attr("time",Date.now())}else if(s>0){var c=Date.now(),g=0;a.find("li").each(function(t,a){var n=$(this);parseInt(n.attr("time"))+g+i.timeShow<=c&&n.css({display:"block",width:n.width(),height:n.height()}).animate({opacity:0,marginLeft:e.width()+"px",marginTop:"-"+n.height()+"px"},100,function(){n.remove()}),g+=i.timeShow})}}},100)}}});