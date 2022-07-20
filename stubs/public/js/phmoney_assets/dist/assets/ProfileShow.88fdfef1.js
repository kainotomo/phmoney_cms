var O=Object.defineProperty,q=Object.defineProperties;var R=Object.getOwnPropertyDescriptors;var T=Object.getOwnPropertySymbols;var E=Object.prototype.hasOwnProperty,K=Object.prototype.propertyIsEnumerable;var L=(t,o,n)=>o in t?O(t,o,{enumerable:!0,configurable:!0,writable:!0,value:n}):t[o]=n,y=(t,o)=>{for(var n in o||(o={}))E.call(o,n)&&L(t,n,o[n]);if(T)for(var n of T(o))K.call(o,n)&&L(t,n,o[n]);return t},g=(t,o)=>q(t,R(o));import{_ as V,r as c,o as a,g as i,a as e,b as s,w as r,n as w,e as B,d as v,h as D,f as u,t as h,j as U,k as S,v as M,l as F,i as b,F as A,m as x,c as k}from"./main.e98d045c.js";const N={data(){return{form:{_method:"PUT",name:this.store.user.name,email:this.store.user.email,photo:null},photoPreview:null}},methods:{async submit(){await this.store.post("/phmoney/user/profile-information",this.form),this.store.errors===null&&(this.store.errors="Saved Successfully!!!",await this.store.loadUser())}}},H={class:"p-6"},I=e("h3",null,"Profile Information",-1),j=e("p",null,"Update your account's profile information and email address.",-1),z={class:"flex flex-wrap gap-2"},Q=e("span",{class:"material-icons-outlined"}," save ",-1);function Y(t,o,n,f,l,m){const d=c("form-label"),_=c("form-input"),C=c("form-button");return a(),i("div",H,[I,j,e("form",{onSubmit:o[2]||(o[2]=B((...p)=>m.submit&&m.submit(...p),["prevent"]))},[e("div",z,[e("div",null,[s(d,{for:"name",value:"Name"}),s(_,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:l.form.name,"onUpdate:modelValue":o[0]||(o[0]=p=>l.form.name=p),autocomplete:"name",required:""},null,8,["modelValue"])]),e("div",null,[s(d,{for:"email",value:"Email"}),s(_,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:l.form.email,"onUpdate:modelValue":o[1]||(o[1]=p=>l.form.email=p),required:""},null,8,["modelValue"])])]),s(C,{class:w(["mt-4",{"opacity-25":t.store.processing}]),disabled:t.store.processing,title:"Save"},{default:r(()=>[Q]),_:1},8,["class","disabled"])],32)])}var G=V(N,[["render",Y]]);const W={data(){return{form:{current_password:"",password:"",password_confirmation:""}}},methods:{async submit(){await this.store.put("/phmoney/user/password",this.form),this.store.errors===null&&(this.store.errors="Saved Successfully!!!",await this.store.loadUser())}}},J={class:"p-6"},X=e("h3",null,"Update Password",-1),Z=e("p",null,"Ensure your account is using a long, random password to stay secure.",-1),oo={class:"flex flex-wrap gap-2"},eo=e("span",{class:"material-icons-outlined"}," save ",-1);function so(t,o,n,f,l,m){const d=c("form-label"),_=c("form-input"),C=c("form-button");return a(),i("div",J,[X,Z,e("form",{onSubmit:o[3]||(o[3]=B((...p)=>m.submit&&m.submit(...p),["prevent"]))},[e("div",oo,[e("div",null,[s(d,{for:"current_password",value:"Current Password"}),s(_,{id:"current_password",type:"password",class:"mt-1 block w-full",modelValue:l.form.current_password,"onUpdate:modelValue":o[0]||(o[0]=p=>l.form.current_password=p),ref:"current_password",autocomplete:"current-password",required:""},null,8,["modelValue"])]),e("div",null,[s(d,{for:"password",value:"New Password"}),s(_,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:l.form.password,"onUpdate:modelValue":o[1]||(o[1]=p=>l.form.password=p),ref:"password",autocomplete:"new-password",required:""},null,8,["modelValue"])]),e("div",null,[s(d,{for:"password_confirmation",value:"Confirm Password"}),s(_,{id:"password_confirmation",type:"password",class:"mt-1 block w-full",modelValue:l.form.password_confirmation,"onUpdate:modelValue":o[2]||(o[2]=p=>l.form.password_confirmation=p),autocomplete:"new-password",required:""},null,8,["modelValue"])])]),s(C,{class:w(["mt-4",{"opacity-25":t.store.processing}]),disabled:t.store.processing,title:"Save"},{default:r(()=>[eo]),_:1},8,["class","disabled"])],32)])}var to=V(W,[["render",so]]);const ro={class:"mt-4"},no={class:"text-sm text-red-600"},ao=e("span",{class:"material-icons-outlined"}," cancel ",-1),io=e("span",{class:"material-icons-outlined"}," check ",-1),lo={emits:["confirmed"],props:{title:{default:"Confirm Password"},content:{default:"For your security, please confirm your password to continue."},button:{default:"Confirm"}},data(){return{confirmingPassword:!1,form:{password:"",error:""},error:null}},methods:{async startConfirmingPassword(){(await this.store.get("/phmoney/user/confirmed-password-status")).confirmed?this.$emit("confirmed"):(this.confirmingPassword=!0,setTimeout(()=>this.$refs.password.focus(),250))},async confirmPassword(){await this.store.post("/phmoney/user/confirm-password",{password:this.form.password}),this.store.errors===null?(this.closeModal(),this.$nextTick(()=>this.$emit("confirmed"))):(this.error=this.store.errors.response.data.errors.password[0],this.$refs.password.focus())},closeModal(){this.confirmingPassword=!1,this.form.password="",this.form.error=""}}},$=v(g(y({},lo),{name:"FormConfirmsPassword",setup(t){return(o,n)=>{const f=c("form-input"),l=c("form-secondary-button"),m=c("form-button");return a(),i("span",null,[e("span",{onClick:n[0]||(n[0]=(...d)=>o.startConfirmingPassword&&o.startConfirmingPassword(...d))},[D(o.$slots,"default")]),s(F,{show:o.confirmingPassword,onClose:o.closeModal},{title:r(()=>[u(h(t.title),1)]),content:r(()=>[u(h(t.content)+" ",1),e("div",ro,[s(f,{type:"password",class:"mt-1 block w-3/4",placeholder:"Password",ref:"password",modelValue:o.form.password,"onUpdate:modelValue":n[1]||(n[1]=d=>o.form.password=d),onKeyup:U(o.confirmPassword,["enter"])},null,8,["modelValue","onKeyup"]),S(e("div",null,[e("p",no,h(o.error),1)],512),[[M,o.error]])])]),footer:r(()=>[s(l,{onClick:o.closeModal,title:"Cancel"},{default:r(()=>[ao]),_:1},8,["onClick"]),s(m,{class:w(["ml-2",{"opacity-25":o.store.processing}]),onClick:o.confirmPassword,disabled:o.store.processing,title:t.button},{default:r(()=>[io]),_:1},8,["onClick","class","disabled","title"])]),_:1},8,["show","onClose"])])}}})),co={props:{type:{type:String,default:"submit"}}},uo=["type"];function mo(t,o,n,f,l,m){return a(),i("button",{type:n.type,class:"inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-red-600 hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-100 disabled:opacity-50 transition"},[D(t.$slots,"default")],8,uo)}var P=V(co,[["render",mo]]);const fo={class:"p-6"},po=e("h3",null,"Two Factor Authentication",-1),ho=e("p",null,"Add additional security to your account using two factor authentication.",-1),_o={key:0},wo={key:1},yo=e("div",{class:"mt-3 max-w-xl text-sm text-gray-600"},[e("p",null," When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application. ")],-1),go={key:2},bo={key:0},vo=e("div",{class:"mt-4 max-w-xl text-sm text-gray-600"},[e("p",{class:"font-semibold"}," Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application. ")],-1),$o=["innerHTML"],Co={key:1},ko=e("div",{class:"mt-4 max-w-xl text-sm text-gray-600"},[e("p",{class:"font-semibold"}," Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost. ")],-1),Po={class:"grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg"},Vo={class:"mt-5"},Uo={key:0},So=u(" Enable "),Mo={key:1},Fo=u(" Regenerate Recovery Codes "),To=u(" Show Recovery Codes "),Lo=u(" Disable "),Bo={data(){return{enabling:!1,disabling:!1,qrCode:null,recoveryCodes:[]}},methods:{async enableTwoFactorAuthentication(){this.enabling=!0,await this.store.post("/phmoney/user/two-factor-authentication",{}),this.store.errors===null&&(await this.showQrCode(),await this.showRecoveryCodes()),this.enabling=!1,this.store.loadUser()},async showQrCode(){let t=await this.store.get("/phmoney/user/two-factor-qr-code");this.qrCode=t.svg},async showRecoveryCodes(){let t=await this.store.get("/phmoney/user/two-factor-recovery-codes");this.recoveryCodes=t},async regenerateRecoveryCodes(){await this.store.post("/phmoney/user/two-factor-recovery-codes"),this.showRecoveryCodes()},async disableTwoFactorAuthentication(){this.disabling=!0,await this.store.delete("/phmoney/user/two-factor-authentication"),this.disabling=!1,this.store.loadUser()}},computed:{twoFactorEnabled(){return!this.enabling&&this.store.user.two_factor_enabled}}},Do=v(g(y({},Bo),{name:"ProfileTwoFactorAuthentication",setup(t){return(o,n)=>{const f=c("form-button"),l=c("form-secondary-button");return a(),i("div",fo,[po,ho,o.twoFactorEnabled?(a(),i("h4",_o," You have enabled two factor authentication. ")):(a(),i("p",wo," You have not enabled two factor authentication. ")),yo,o.twoFactorEnabled?(a(),i("div",go,[o.qrCode?(a(),i("div",bo,[vo,e("div",{class:"mt-4",innerHTML:o.qrCode},null,8,$o)])):b("",!0),o.recoveryCodes.length>0?(a(),i("div",Co,[ko,e("div",Po,[(a(!0),i(A,null,x(o.recoveryCodes,m=>(a(),i("div",{key:m},h(m),1))),128))])])):b("",!0)])):b("",!0),e("div",Vo,[o.twoFactorEnabled?(a(),i("div",Mo,[s($,{onConfirmed:o.regenerateRecoveryCodes},{default:r(()=>[o.recoveryCodes.length>0?(a(),k(l,{key:0,class:"mr-3"},{default:r(()=>[Fo]),_:1})):b("",!0)]),_:1},8,["onConfirmed"]),s($,{onConfirmed:o.showRecoveryCodes},{default:r(()=>[o.recoveryCodes.length===0?(a(),k(l,{key:0,class:"mr-3"},{default:r(()=>[To]),_:1})):b("",!0)]),_:1},8,["onConfirmed"]),s($,{onConfirmed:o.disableTwoFactorAuthentication},{default:r(()=>[s(P,{class:w({"opacity-25":o.disabling}),disabled:o.disabling},{default:r(()=>[Lo]),_:1},8,["class","disabled"])]),_:1},8,["onConfirmed"])])):(a(),i("div",Uo,[s($,{onConfirmed:o.enableTwoFactorAuthentication},{default:r(()=>[s(f,{type:"button",class:w({"opacity-25":o.enabling}),disabled:o.enabling},{default:r(()=>[So]),_:1},8,["class","disabled"])]),_:1},8,["onConfirmed"])]))])])}}})),Ao={class:"p-6"},xo=e("h3",null,"Browser Sessions",-1),Oo=e("p",null,"Manage and log out your active sessions on other browsers and devices.",-1),qo=e("div",{class:"max-w-xl text-sm text-gray-600"}," If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password. ",-1),Ro={key:0,class:"mt-5 space-y-6"},Eo={key:0,fill:"none","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",viewBox:"0 0 24 24",stroke:"currentColor",class:"w-8 h-8 text-gray-500"},Ko=e("path",{d:"M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"},null,-1),No=[Ko],Ho={key:1,xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24","stroke-width":"2",stroke:"currentColor",fill:"none","stroke-linecap":"round","stroke-linejoin":"round",class:"w-8 h-8 text-gray-500"},Io=e("path",{d:"M0 0h24v24H0z",stroke:"none"},null,-1),jo=e("rect",{x:"7",y:"4",width:"10",height:"16",rx:"1"},null,-1),zo=e("path",{d:"M11 5h2M12 17v.01"},null,-1),Qo=[Io,jo,zo],Yo={class:"ml-3"},Go={class:"text-sm text-gray-600"},Wo={class:"text-xs text-gray-500"},Jo={key:0,class:"text-sky-500 font-semibold"},Xo={key:1},Zo={class:"flex items-center mt-5"},oe=u(" Log Out Other Browser Sessions "),ee=u(" Log Out Other Browser Sessions "),se=u(" Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices. "),te={class:"mt-4"},re={class:"text-sm text-red-600"},ne=u(" Cancel "),ae=u(" Log Out Other Browser Sessions "),ie={data(){return{confirmingLogout:!1,form:{password:""},error:null}},methods:{confirmLogout(){this.confirmingLogout=!0,setTimeout(()=>this.$refs.password.focus(),250)},async logoutOtherBrowserSessions(){await this.store.delete("/phmoney/user/other-browser-sessions",this.form),this.store.errors===null?(await this.store.loadUser(),this.closeModal()):(this.error=this.store.errors.response.data.message,this.$refs.password.focus())},closeModal(){this.confirmingLogout=!1,this.form.password=""}}},le=v(g(y({},ie),{name:"ProfileSessions",setup(t){return(o,n)=>{const f=c("form-button"),l=c("form-input"),m=c("form-secondary-button");return a(),i("div",Ao,[xo,Oo,qo,o.store.props.sessions?(a(),i("div",Ro,[(a(!0),i(A,null,x(o.store.props.sessions,(d,_)=>(a(),i("div",{class:"flex items-center",key:_},[e("div",null,[d.agent.is_desktop?(a(),i("svg",Eo,No)):(a(),i("svg",Ho,Qo))]),e("div",Yo,[e("div",Go,h(d.agent.platform)+" - "+h(d.agent.browser),1),e("div",null,[e("div",Wo,[u(h(d.ip_address)+", ",1),d.is_current_device?(a(),i("span",Jo,"This device")):(a(),i("span",Xo,"Last active "+h(d.last_active),1))])])])]))),128))])):b("",!0),e("div",Zo,[s(f,{onClick:o.confirmLogout},{default:r(()=>[oe]),_:1},8,["onClick"])]),s(F,{show:o.confirmingLogout,onClose:o.closeModal},{title:r(()=>[ee]),content:r(()=>[se,e("div",te,[s(l,{type:"password",class:"mt-1 block w-3/4",placeholder:"Password",ref:"password",modelValue:o.form.password,"onUpdate:modelValue":n[0]||(n[0]=d=>o.form.password=d),onKeyup:U(o.logoutOtherBrowserSessions,["enter"])},null,8,["modelValue","onKeyup"]),S(e("div",null,[e("p",re,h(o.error),1)],512),[[M,o.error]])])]),footer:r(()=>[s(m,{onClick:o.closeModal},{default:r(()=>[ne]),_:1},8,["onClick"]),s(f,{class:w(["ml-2",{"opacity-25":o.store.processing}]),onClick:o.logoutOtherBrowserSessions,disabled:o.store.processing},{default:r(()=>[ae]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show","onClose"])])}}})),de={class:"p-6"},ce=e("h3",null,"Delete Account",-1),ue=e("p",null,"Permanently delete your account.",-1),me=e("div",{class:"max-w-xl text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. ",-1),fe={class:"mt-5"},pe=u(" Delete Account "),he=u(" Delete Account "),_e=u(" Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. "),we={class:"mt-4"},ye={class:"text-sm text-red-600"},ge=u(" Cancel "),be=u(" Delete Account "),ve={data(){return{confirmingUserDeletion:!1,form:{password:""},error:null}},methods:{confirmUserDeletion(){this.confirmingUserDeletion=!0,setTimeout(()=>this.$refs.password.focus(),250)},async deleteUser(){await this.store.delete("/phmoney/user",this.form),this.store.errors===null?this.$router.push({name:"auth.login"}):(this.error=this.store.errors.response.data.message,this.$refs.password.focus())},closeModal(){this.confirmingUserDeletion=!1,this.form.reset()}}},$e=v(g(y({},ve),{name:"ProfileDelete",setup(t){return(o,n)=>{const f=c("form-input"),l=c("form-secondary-button");return a(),i("div",de,[ce,ue,me,e("div",fe,[s(P,{onClick:o.confirmUserDeletion},{default:r(()=>[pe]),_:1},8,["onClick"])]),s(F,{show:o.confirmingUserDeletion,onClose:o.closeModal},{title:r(()=>[he]),content:r(()=>[_e,e("div",we,[s(f,{type:"password",class:"mt-1 block w-3/4",placeholder:"Password",ref:"password",modelValue:o.form.password,"onUpdate:modelValue":n[0]||(n[0]=m=>o.form.password=m),onKeyup:U(o.deleteUser,["enter"])},null,8,["modelValue","onKeyup"]),S(e("div",null,[e("p",ye,h(o.error),1)],512),[[M,o.error]])])]),footer:r(()=>[s(l,{onClick:o.closeModal},{default:r(()=>[ge]),_:1},8,["onClick"]),s(P,{class:w(["ml-2",{"opacity-25":o.store.processing}]),onClick:o.deleteUser,disabled:o.store.processing},{default:r(()=>[be]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show","onClose"])])}}})),Ce={class:"bg-white shadow mt-4 prose max-w-none"},ke={class:"bg-white shadow mt-4 prose max-w-none"},Pe={class:"bg-white shadow mt-4 prose max-w-none"},Ve={class:"bg-white shadow mt-4 prose max-w-none"},Ue={class:"bg-white shadow mt-4 prose max-w-none"},Se={async created(){await this.store.get("/phmoney/user/profile")}},Te=v(g(y({},Se),{name:"ProfileShow",setup(t){return(o,n)=>{const f=c("FormLayout");return a(),k(f,{title:"User Profile"},{default:r(()=>[e("div",Ce,[s(G)]),e("div",ke,[s(to)]),e("div",Pe,[s(Do)]),e("div",Ve,[s(le)]),e("div",Ue,[s($e)])]),_:1})}}}));export{Te as default};
