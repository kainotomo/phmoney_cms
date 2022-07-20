var S=Object.defineProperty,C=Object.defineProperties;var j=Object.getOwnPropertyDescriptors;var T=Object.getOwnPropertySymbols;var B=Object.prototype.hasOwnProperty,N=Object.prototype.propertyIsEnumerable;var V=(l,e,o)=>e in l?S(l,e,{enumerable:!0,configurable:!0,writable:!0,value:o}):l[e]=o,U=(l,e)=>{for(var o in e||(e={}))B.call(e,o)&&V(l,o,e[o]);if(T)for(var o of T(e))N.call(e,o)&&V(l,o,e[o]);return l},w=(l,e)=>C(l,j(e));import{_ as h,r as d,o as u,g as p,b as n,a as s,t as _,n as r,w as v,e as g,k as x,p as k,F as R,m as F,d as P,c as D,i as E}from"./main.e98d045c.js";const L={props:["team","permissions"],data(){return{form:{name:this.team.name}}},methods:{async submit(){try{await this.store.axios.put(`/phmoney/teams/${this.$route.params.team_pk}`,this.form)}catch{}await this.store.get(`/phmoney/teams/${this.$route.params.team_pk}`)}}},I={class:"p-6"},O=s("h3",null,"Team Name",-1),q=s("p",null,"The team's name and owner information.",-1),A={class:"flex items-center mt-2"},J=["src","alt"],z={class:"ml-4 leading-tight"},W={class:"text-sm text-gray-700"},G={class:"flex flex-wrap gap-2"},H=s("span",{class:"material-icons-outlined"}," save ",-1);function K(l,e,o,y,a,c){const i=d("form-label"),b=d("form-input"),f=d("form-button");return u(),p("div",I,[O,q,n(i,{value:"Team Owner"}),s("div",A,[s("img",{class:"object-cover w-12 h-12 rounded-full",src:l.store.user.profile_photo_url,alt:l.store.user.name},null,8,J),s("div",z,[s("div",null,_(l.store.user.name),1),s("div",W,_(l.store.user.email),1)])]),s("form",{onSubmit:e[1]||(e[1]=g((...m)=>c.submit&&c.submit(...m),["prevent"]))},[s("div",G,[n(i,{for:"name",value:"Team Name"}),n(b,{id:"name",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.name,"onUpdate:modelValue":e[0]||(e[0]=m=>a.form.name=m),required:"",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),n(f,{class:r(["mt-4",{"opacity-25":l.store.processing}]),disabled:l.store.processing,title:"Save"},{default:v(()=>[H]),_:1},8,["class","disabled"])],32)])}var Q=h(L,[["render",K]]);const X={props:["team","permissions","options"],data(){return{form:{options:{accounting_period:{date_start:this.options.accounting_period.date_start,date_end:this.options.accounting_period.date_end},business:{company_name:this.options.business.company_name,company_address:this.options.business.company_address,company_contact_person:this.options.business.company_contact_person,company_phone_number:this.options.business.company_phone_number,company_fax_number:this.options.business.company_fax_number,company_email_address:this.options.business.company_email_address,company_website_url:this.options.business.company_website_url,company_id:this.options.business.company_id,default_customer_taxtable:this.options.business.default_customer_taxtable,default_vendor_taxtable:this.options.business.default_vendor_taxtable},tax:{tax_number:this.options.tax.tax_number},counters:{bill:this.options.counters.bill,vendor:this.options.counters.vendor,invoice:this.options.counters.invoice,job:this.options.counters.job,employee:this.options.counters.employee},counter_formats:{bill:this.options.counter_formats.bill,vendor:this.options.counter_formats.vendor,invoice:this.options.counter_formats.invoice,job:this.options.counter_formats.job,employee:this.options.counter_formats.employee}}}}},methods:{async submit(){await this.store.put(`/phmoney/teams/${this.$route.params.team_pk}/options/store`,this.form),await this.store.get(`/phmoney/teams/${this.$route.params.team_pk}`)}}},Y={class:"p-6"},Z=s("h3",null,"Options",-1),$=s("p",null,"The team's business options.",-1),ee={class:"grid grid-cols-6 gap-6"},se=s("div",{class:"col-span-6 sm:col-span-6"},[s("h4",{class:"text-md font-bold text-gray-900"},"Accounting Period")],-1),oe={class:"col-span-6 sm:col-span-3"},ne={class:"col-span-6 sm:col-span-3"},te=s("div",{class:"col-span-6 sm:col-span-6"},[s("h4",{class:"text-md font-bold text-gray-900"},"Business")],-1),ae={class:"col-span-6 sm:col-span-2"},le={class:"col-span-6 sm:col-span-2"},ie={class:"col-span-6 sm:col-span-2"},me={class:"col-span-6 sm:col-span-2"},re={class:"col-span-6 sm:col-span-2"},de={class:"col-span-6 sm:col-span-2"},ue={class:"col-span-6 sm:col-span-4"},pe={class:"col-span-6 sm:col-span-2"},ce={class:"col-span-6 sm:col-span-2"},_e=s("option",{value:null,class:"text-gray-500"},"Select Tax Table",-1),be=["value"],fe={class:"col-span-6 sm:col-span-4"},ye=s("option",{value:null,class:"text-gray-500"},"Select Tax Table",-1),ve=["value"],he=s("div",{class:"col-span-6 sm:col-span-6 border-t pt-2"},[s("h4",{class:"text-md font-bold text-gray-900"},"Tax")],-1),ge={class:"col-span-6 sm:col-span-2"},Te=s("div",{class:"col-span-6 sm:col-span-6 border-t pt-2"},[s("h4",{class:"text-md font-bold text-gray-900"},"Counters")],-1),Ve={class:"col-span-6 sm:col-span-2"},Ue={class:"col-span-6 sm:col-span-4"},we={class:"col-span-6 sm:col-span-2"},xe={class:"col-span-6 sm:col-span-4"},ke={class:"col-span-6 sm:col-span-2"},Re={class:"col-span-6 sm:col-span-4"},Fe={class:"col-span-6 sm:col-span-2"},Me={class:"col-span-6 sm:col-span-4"},Se={class:"col-span-6 sm:col-span-2"},Ce={class:"col-span-6 sm:col-span-4"},je=s("span",{class:"material-icons-outlined"}," save ",-1);function Be(l,e,o,y,a,c){const i=d("form-label"),b=d("date-picker-start"),f=d("date-picker-end"),m=d("form-input"),M=d("form-button");return u(),p("div",Y,[Z,$,s("form",{onSubmit:e[23]||(e[23]=g((...t)=>c.submit&&c.submit(...t),["prevent"]))},[s("div",ee,[se,s("div",oe,[n(i,{for:"accounting_period_date_start",value:"Start Date"}),n(b,{id:"accounting_period_date_start",name:"accounting_period_date_start",modelValue:a.form.options.accounting_period.date_start,"onUpdate:modelValue":e[0]||(e[0]=t=>a.form.options.accounting_period.date_start=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled"])]),s("div",ne,[n(i,{for:"accounting_period_date_end",value:"End Date"}),n(f,{id:"accounting_period_date_end",name:"accounting_period_date_end",modelValue:a.form.options.accounting_period.date_end,"onUpdate:modelValue":e[1]||(e[1]=t=>a.form.options.accounting_period.date_end=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled"])]),te,s("div",ae,[n(i,{for:"business_company_name",value:"Company Name"}),n(m,{id:"business_company_name",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_name,"onUpdate:modelValue":e[2]||(e[2]=t=>a.form.options.business.company_name=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",le,[n(i,{for:"business_company_address",value:"Company Address"}),n(m,{id:"business_company_address",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_address,"onUpdate:modelValue":e[3]||(e[3]=t=>a.form.options.business.company_address=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",ie,[n(i,{for:"business_company_contact_person",value:"Company Contact Person"}),n(m,{id:"business_company_contact_person",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_contact_person,"onUpdate:modelValue":e[4]||(e[4]=t=>a.form.options.business.company_contact_person=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",me,[n(i,{for:"business_company_phone_number",value:"Company Phone Number"}),n(m,{id:"business_company_phone_number",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_phone_number,"onUpdate:modelValue":e[5]||(e[5]=t=>a.form.options.business.company_phone_number=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",re,[n(i,{for:"business_company_fax_number",value:"Company Fax Number"}),n(m,{id:"business_company_fax_number",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_fax_number,"onUpdate:modelValue":e[6]||(e[6]=t=>a.form.options.business.company_fax_number=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",de,[n(i,{for:"business_company_email_address",value:"Company Email Number"}),n(m,{id:"business_company_email_address",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_email_address,"onUpdate:modelValue":e[7]||(e[7]=t=>a.form.options.business.company_email_address=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",ue,[n(i,{for:"business_company_website_url",value:"Company Website Url"}),n(m,{id:"business_company_website_url",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_website_url,"onUpdate:modelValue":e[8]||(e[8]=t=>a.form.options.business.company_website_url=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",pe,[n(i,{for:"business_company_id",value:"Company ID"}),n(m,{id:"business_company_id",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.business.company_id,"onUpdate:modelValue":e[9]||(e[9]=t=>a.form.options.business.company_id=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",ce,[n(i,{for:"business_default_customer_taxtable",value:"Default Customer Tax Table"}),x(s("select",{id:"business_default_customer_taxtable","onUpdate:modelValue":e[10]||(e[10]=t=>a.form.options.business.default_customer_taxtable=t),class:"border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[_e,(u(!0),p(R,null,F(l.store.props.taxtables,t=>(u(),p("option",{key:t.guid,value:t},_(t.name),9,be))),128))],512),[[k,a.form.options.business.default_customer_taxtable]])]),s("div",fe,[n(i,{for:"business_default_vendor_taxtable",value:"Default Vendor Tax Table"}),x(s("select",{id:"business_default_vendor_taxtable","onUpdate:modelValue":e[11]||(e[11]=t=>a.form.options.business.default_vendor_taxtable=t),class:"border-gray-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"},[ye,(u(!0),p(R,null,F(l.store.props.taxtables,t=>(u(),p("option",{key:t.guid,value:t},_(t.name),9,ve))),128))],512),[[k,a.form.options.business.default_vendor_taxtable]])]),he,s("div",ge,[n(i,{for:"tax_number",value:"Tax Number"}),n(m,{id:"tax_number",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.tax.tax_number,"onUpdate:modelValue":e[12]||(e[12]=t=>a.form.options.tax.tax_number=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),Te,s("div",Ve,[n(i,{for:"bill_number",value:"Bill number"}),n(m,{id:"bill_number",type:"number",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counters.bill,"onUpdate:modelValue":e[13]||(e[13]=t=>a.form.options.counters.bill=t),min:"0",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Ue,[n(i,{for:"bill_number_format",value:"Bill number format"}),n(m,{id:"bill_number_format",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counter_formats.bill,"onUpdate:modelValue":e[14]||(e[14]=t=>a.form.options.counter_formats.bill=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",we,[n(i,{for:"vendor_number",value:"Vendor number"}),n(m,{id:"vendor_number",type:"number",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counters.vendor,"onUpdate:modelValue":e[15]||(e[15]=t=>a.form.options.counters.vendor=t),min:"0",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",xe,[n(i,{for:"vendor_number_format",value:"Vendor number format"}),n(m,{id:"vendor_number_format",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counter_formats.vendor,"onUpdate:modelValue":e[16]||(e[16]=t=>a.form.options.counter_formats.vendor=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",ke,[n(i,{for:"invoice_number",value:"Invoice number"}),n(m,{id:"invoice_number",type:"number",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counters.invoice,"onUpdate:modelValue":e[17]||(e[17]=t=>a.form.options.counters.invoice=t),min:"0",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Re,[n(i,{for:"vendor_number_format",value:"Invoice number format"}),n(m,{id:"invoice_number_format",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counter_formats.invoice,"onUpdate:modelValue":e[18]||(e[18]=t=>a.form.options.counter_formats.invoice=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Fe,[n(i,{for:"job_number",value:"Job number"}),n(m,{id:"job_number",type:"number",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counters.job,"onUpdate:modelValue":e[19]||(e[19]=t=>a.form.options.counters.job=t),min:"0",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Me,[n(i,{for:"job_number_format",value:"Job number format"}),n(m,{id:"job_number_format",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counter_formats.job,"onUpdate:modelValue":e[20]||(e[20]=t=>a.form.options.counter_formats.job=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Se,[n(i,{for:"employee_number",value:"Employee number"}),n(m,{id:"employee_number",type:"number",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counters.employee,"onUpdate:modelValue":e[21]||(e[21]=t=>a.form.options.counters.employee=t),min:"0",disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),s("div",Ce,[n(i,{for:"job_number_format",value:"Employee number format"}),n(m,{id:"employee_number_format",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!o.permissions.canUpdateTeam}]),modelValue:a.form.options.counter_formats.employee,"onUpdate:modelValue":e[22]||(e[22]=t=>a.form.options.counter_formats.employee=t),disabled:!o.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])])]),n(M,{class:r(["mt-4",{"opacity-25":l.store.processing}]),disabled:l.store.processing,title:"Save"},{default:v(()=>[je]),_:1},8,["class","disabled"])],32)])}var Ne=h(X,[["render",Be]]);const Pe={props:["team","availableRoles","userPermissions"],data(){return{addTeamMemberForm:{email:"",role:null},updateRoleForm:{role:null},leaveTeamForm:{},removeTeamMemberForm:{},currentlyManagingRole:!1,managingRoleFor:null,confirmingLeavingTeam:!1,teamMemberBeingRemoved:null}},methods:{async addTeamMember(){try{await this.store.axios.post(`/phmoney/teams/${this.$route.params.team_pk}/members`,this.addTeamMemberForm)}catch{}await this.store.get(`/phmoney/teams/${this.$route.params.team_pk}`)},async cancelTeamInvitation(l){try{await this.store.axios.delete(`/phmoney/team-invitations/${l.id}`)}catch{}},manageRole(l){this.managingRoleFor=l,this.updateRoleForm.role=l.membership.role,this.currentlyManagingRole=!0},async updateRole(){await this.store.get(`/phmoney/teams/${this.$route.params.team_pk}`),this.updateRoleForm.put(route("team-members.update",[this.team,this.managingRoleFor]),{preserveScroll:!0,onSuccess:()=>this.currentlyManagingRole=!1})},confirmLeavingTeam(){this.confirmingLeavingTeam=!0},leaveTeam(){this.leaveTeamForm.delete(route("team-members.destroy",[this.team,this.$page.props.user]))},confirmTeamMemberRemoval(l){this.teamMemberBeingRemoved=l},removeTeamMember(){this.removeTeamMemberForm.delete(route("team-members.destroy",[this.team,this.teamMemberBeingRemoved]),{errorBag:"removeTeamMember",preserveScroll:!0,preserveState:!0,onSuccess:()=>this.teamMemberBeingRemoved=null})},displayableRole(l){return this.availableRoles.find(e=>e.key===l).name}}},De={class:"p-6"},Ee=s("h3",null,"Team Name",-1),Le=s("p",null,"The team's name and owner information.",-1),Ie={class:"flex items-center mt-2"},Oe=["src","alt"],qe={class:"ml-4 leading-tight"},Ae={class:"text-sm text-gray-700"},Je={class:"flex flex-wrap gap-2"},ze=s("span",{class:"material-icons-outlined"}," save ",-1);function We(l,e,o,y,a,c){const i=d("form-label"),b=d("form-input"),f=d("form-button");return u(),p("div",De,[Ee,Le,n(i,{value:"Team Owner"}),s("div",Ie,[s("img",{class:"object-cover w-12 h-12 rounded-full",src:l.store.user.profile_photo_url,alt:l.store.user.name},null,8,Oe),s("div",qe,[s("div",null,_(l.store.user.name),1),s("div",Ae,_(l.store.user.email),1)])]),s("form",{onSubmit:e[1]||(e[1]=g((...m)=>l.submit&&l.submit(...m),["prevent"]))},[s("div",Je,[n(i,{for:"name",value:"Team Name"}),n(b,{id:"name",type:"text",class:r(["mt-1 block w-full",{"opacity-50":!l.permissions.canUpdateTeam}]),modelValue:l.form.name,"onUpdate:modelValue":e[0]||(e[0]=m=>l.form.name=m),required:"",disabled:!l.permissions.canUpdateTeam},null,8,["modelValue","disabled","class"])]),n(f,{class:r(["mt-4",{"opacity-25":l.store.processing}]),disabled:l.store.processing,title:"Save"},{default:v(()=>[ze]),_:1},8,["class","disabled"])],32)])}var Ge=h(Pe,[["render",We]]);const He={class:"bg-white shadow mt-4 prose max-w-none"},Ke={class:"bg-white shadow mt-4 prose max-w-none"},Qe={class:"bg-white shadow mt-4 prose max-w-none"},Xe={async created(){await this.store.get(`/phmoney/teams/${this.$route.params.team_pk}`)}},$e=P(w(U({},Xe),{name:"TeamsShow",setup(l){return(e,o)=>{const y=d("FormLayout");return e.store.props.team?(u(),D(y,{key:0,title:"User Profile"},{default:v(()=>[s("div",He,[n(Q,{team:e.store.props.team,permissions:e.store.props.permissions},null,8,["team","permissions"])]),s("div",Ke,[n(Ne,{team:e.store.props.team,permissions:e.store.props.permissions,options:e.store.props.options},null,8,["team","permissions","options"])]),s("div",Qe,[n(Ge,{team:e.store.props.team,availableRoles:e.store.props.availableRoles,userPermissions:e.store.props.userPermissions},null,8,["team","availableRoles","userPermissions"])])]),_:1})):E("",!0)}}}));export{$e as default};
