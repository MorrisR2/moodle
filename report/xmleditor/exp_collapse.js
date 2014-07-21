 function findAncestor(el, comparisonFunc) {
    if (comparisonFunc(el) == true) return el;
    else if (el.parentNode) {
      return arguments.callee(el.parentNode, comparisonFunc);
    }
    else return false;
  }

function setEvent(){

	var button = document.getElementById('tog').getElementsByTagName('button');
	var i=0;
	while(button[i]){
		if (button[i].className == 'expandcollapse') {
			var dt = findAncestor(button[i],  function(el) { return el.tagName == 'DT' });
			
			button[i].onclick=function(){ toggleDisplay( findAncestor(this,  function(el) { return el.tagName == 'DT' }) ) };
		}
		i++
	}
	var dd=document.getElementById('tog').getElementsByTagName('dd');
    var i=0;
    while(dd[i]){
		if (dd[i].getElementsByTagName('dd') && dd[i].getElementsByTagName('dd').length > 0) {
        	dd[i].className='hide';
		}
        i++;
    }
	// toggleDisplay()
}


function toggleDisplay(currentEl){
	var thesibling;
    if(currentEl){
        if(currentEl.nextSibling.nodeType==3){  // moz, ff
            thesibling = currentEl.nextSibling.nextSibling;
        } else{
            thesibling = currentEl.nextSibling;
        }
		if (thesibling.className == 'show') {
			thesibling.className='hide';
			currentEl.className='expandable';
		} else {
			thesibling.className = 'show';
			currentEl.className='collapsible';
		}
	}

}


