var m=Object.defineProperty,c=Object.defineProperties;var _=Object.getOwnPropertyDescriptors;var s=Object.getOwnPropertySymbols;var u=Object.prototype.hasOwnProperty,l=Object.prototype.propertyIsEnumerable;var r=(t,e,o)=>e in t?m(t,e,{enumerable:!0,configurable:!0,writable:!0,value:o}):t[e]=o,a=(t,e)=>{for(var o in e||(e={}))u.call(e,o)&&r(t,o,e[o]);if(s)for(var o of s(e))l.call(e,o)&&r(t,o,e[o]);return t},n=(t,e)=>c(t,_(e));import{V as h}from"./VendorsEdit.46c044b0.js";import{d as f,r as v,o as d,c as i,w,a as V,i as y}from"./main.e98d045c.js";const k={class:"bg-white shadow mt-4 prose max-w-none"},C={async created(){await this.store.get(`/phmoney/business/vendors/edit/${this.$route.params.vendor_pk}`)}},E=f(n(a({},C),{name:"VendorsEdit",setup(t){return(e,o)=>{const p=v("FormLayout");return d(),i(p,{title:`Edit Vendor - ${e.store.props.vendor?e.store.props.vendor.name:"..."}`},{default:w(()=>[V("div",k,[e.store.processing?y("",!0):(d(),i(h,{key:0}))])]),_:1},8,["title"])}}}));export{E as default};
