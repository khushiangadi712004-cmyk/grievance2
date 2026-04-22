

function togglePassword(){

let password=document.getElementById("password");
let eye=document.getElementById("eye");

if(password.type==="password"){
password.type="text";
eye.classList.remove("fa-eye");
eye.classList.add("fa-eye-slash");
}
else{
password.type="password";
eye.classList.remove("fa-eye-slash");
eye.classList.add("fa-eye");
}

}
/* captcha */

function generateCaptcha(){

let captcha=Math.floor(1000 + Math.random()*9000);

document.getElementById("captcha").innerText=captcha;

localStorage.setItem("captchaCode",captcha);

}

/* captcha validation */

function validateCaptcha(){

let entered=document.getElementById("captchaInput").value;
let real=localStorage.getItem("captchaCode");

if(entered==real){
alert("Login successful");
}
else{
alert("Invalid captcha");
generateCaptcha();
}

}

window.onload=generateCaptcha;


