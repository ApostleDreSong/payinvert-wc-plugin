var PayinvertNS=PayinvertNS||{};PayinvertNS.Payinvert=class{constructor({firstName:e,lastName:t,mobile:n,country:i,email:r,option:s,currency:a,encryptionKey:o,apiKey:h,amount:l,reference:p,description:d,onCompleted:c,onError:m,onClose:y}){this.onClose=y,this.first_name=e,this.last_name=t,this.mobile=n,this.country=i,this.email=r,this.currency=a,this.option=s,this.amount=l,this.description=d,this.apiKey=h,this.encryptionKey=o,this.reference=p,this.amount=l,this.onCompleted=c,this.onError=m,this.body=document?.getElementsByTagName("body")[0],this.parentDiv=document?.createElement("div"),this.frameWrapper=document?.createElement("div"),this.PayinvertFrame=document?.createElement("iframe"),this.closeButton=document?.createElement("div"),this.loader=document?.createElement("div"),this.loaderWrapper=document?.createElement("div"),this.head=document.head,this.PayinvertStyle=document?.createElement("style"),this.head.appendChild(this.PayinvertStyle),this.PayinvertStyle.innerHTML="@-webkit-keyframes spin {\n            0% { -webkit-transform: rotate(0deg); }\n            100% { -webkit-transform: rotate(360deg); }\n          }\n          @keyframes spin {\n            0% { transform: rotate(0deg); }\n            100% { transform: rotate(360deg); }\n          }\n        ",this.PayinvertFrame.addEventListener("load",(()=>{this.loaderWrapper.style.display="none"}))}close(){this.parentDiv.style.display="none",this.onClose()}init(){window.addEventListener("message",(e=>{"paymentSuccessful"===e.data.name&&(this.onCompleted(e.data),this.close()),"paymentError"===e.data.name&&this.onError(e.data)})),this.closeButton.innerHTML='<span style="font-weight: 600; font-size: 14px;  display: flex; align-items: center; letter-spacing: -0.011em; color: #E00000; background-color: #FFECEC; border-radius: 8px; justify-content: center; padding: 5px">close</span>',this.closeButton.addEventListener("click",(()=>this.close())),this.body?.appendChild(this.parentDiv),this.parentDiv?.appendChild(this.frameWrapper),this.frameWrapper?.appendChild(this.PayinvertFrame),this.frameWrapper?.appendChild(this.closeButton),this.parentDiv?.appendChild(this.loaderWrapper),this.loaderWrapper?.appendChild(this.loader),this.loaderWrapper.style.cssText="\n                        position: fixed;\n                        width: 100%;\n                        height: 100%;\n                        display: flex;\n                        justify-content: center;\n                        align-items: center;\n        ",this.loader.style.cssText="\n                                border: 3px solid #ED6E24;\n                                border-radius: 50%;\n                                border-top: 3px solid #F0874A;\n                                width: 44px;\n                                height: 44px;\n                                -webkit-animation: spin 2s linear infinite; /* Safari */\n                                animation: spin 2s linear infinite;\n                            ",this.PayinvertFrame.src=this.encryptionKey?`https://payment-checkout.payinvert.com/?reference=${this.reference}&amount=${this.amount}&firstName=${this.firstName}&lastName=${this.lastName}&mobile=${this.mobile}&country=${this.country}&email=${this.email}&option=${this.option}&currency=${this.currency}&encryptionKey=${this.encryptionKey}&apiKey=${this.apiKey}`:`https://payment-checkout.payinvert.com/?reference=${this.reference}&amount=${this.amount}`,this.PayinvertFrame.setAttribute("frame-border",1),this.PayinvertFrame.setAttribute("scrolling",0),this.PayinvertFrame.style.cssText="\n                                border: none;\n                                background-color: transparent;\n                                width: 100%;\n                                height: 100vh;\n                                                               overflow: hidden;\n                                ",this.parentDiv.style.cssText="\n                                display: flex;\n                                justify-content: center;\n                                align-items: center;\n                                position: fixed;\n                                z-index: 10000;\n                                left: 0;\n                                top: 0;\n                                width: 100%;\n                                height: 100%;\n                                overflow: hidden;\n                                background-color: rgba(0,0,0,0.4);\n                                    ",this.frameWrapper.style.cssText="\n                                    background-color: transparent;\n                                    position: relative;\n                                    width: 100%;\n                                    overflow: scroll;\n                                ",this.closeButton.style.cssText="\n                                cursor: pointer;\n                                position: absolute;\n                                color: white;\n                                top: 17px;\n                                right: 28px;\n                                font-size: x-large\n                                    "}};