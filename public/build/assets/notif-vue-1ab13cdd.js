import{_ as u,l,o as n,c as s,a as i,t as d,D as f,F as h,d as g,B as _,C as m,f as p,g as w}from"./_plugin-vue_export-helper-65a45c99.js";const v={setup(){return{unread_count:l(0),user_id:l(0),users:[],legit:!0}},async mounted(){fetch("/user/info").then(t=>t.json()).then(t=>{let e=t.data;this.user_id=e.id,this.legit=e.legit,this.legit||(this.users=["adoy","lao2x","etong"])}).then(()=>{localStorage.getItem("unread")==null&&!this.legit?this.unread_count=this.users.length:this.unread_count=localStorage.getItem("unread")}).catch(t=>console.log(t))},methods:{delay(t){return new Promise(e=>setTimeout(e,t))},redirectTrans(){this.unread_count>0&&(this.unread_count-=1,localStorage.setItem("unread",this.unread_count)),window.location.href="/transactions"},readAllNotif(){localStorage.setItem("unread",0),this.unread_count=0,window.location.href="/transactions"},markAllAsRead(){localStorage.setItem("unread",0),this.unread_count=0},testAlert(){navigator.serviceWorker.register("/js/ws.js"),Notification.requestPermission(function(t){t==="granted"&&navigator.serviceWorker.ready.then(function(e){e.showNotification("Notification with ServiceWorker")})})},triggerAlert(){navigator.serviceWorker.register("/js/ws.js"),Notification.requestPermission(function(t){t==="granted"&&navigator.serviceWorker.ready.then(function(e){e.showNotification("New Cash-in Received!")})}),"Notification"in window?Notification.permission==="granted"?new Notification("New Cash-in Received!"):Notification.permission!=="denied"&&Notification.requestPermission().then(t=>{t==="granted"&&Notification("New Cash-in Received!")}):alert("This browser does not support desktop notification")}}},N=i("i",{class:"far fa-bell"},null,-1),k={key:0,class:"badge bg-danger text-xs px-1 py-0 mr-0"},x={class:"dropdown-menu dropdown-menu-lg dropdown-menu-right hide max-h-80 overflow-scroll",style:{left:"inherit",right:"0px"}},y={class:"dropdown-item dropdown-header"},A=i("i",{class:"fas fa-envelope mr-2"},null,-1),S=i("span",{class:"float-right text-muted text-sm"},"3 mins",-1),b=i("div",{class:"dropdown-divider"},null,-1);function C(t,e,R,o,T,r){return n(),s("div",null,[i("button",{class:"nav-link icon-bell",onClick:e[0]||(e[0]=a=>r.markAllAsRead()),role:"button","data-bs-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"false"},[N,o.unread_count>0?(n(),s("span",k,d(o.unread_count),1)):f("",!0)]),i("div",x,[i("span",y,d(o.unread_count??0)+" Notifications",1),(n(!0),s(h,null,g(o.users,(a,c)=>_((n(),s("div",{key:c},[i("a",{href:"#",onClick:e[1]||(e[1]=W=>r.redirectTrans()),class:"dropdown-item"},[A,p(" "+d(a)+" cash-in ",1),S]),b])),[[m,o.users.length>0]])),128)),i("a",{onClick:e[2]||(e[2]=a=>r.readAllNotif()),id:"allow-notifications",class:"dropdown-item dropdown-footer"},"See All Notifications")])])}const j=u(v,[["render",C]]),I=w(j);I.mount("#notif-nav");
