function base(value){
	this.value=value;
}

base.getValue= function(){
	alert(this.value);
}

function child(value){
	this.prototype=base;
	this.base(value);
	alert(test);
}