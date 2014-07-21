	// SR Expand Collapse Script (Shaun Rambaran - scribblefactory.net)

		function openECAll()
			{
			var arrayReturn=document.getElementsByTagName('dt');
			J=arrayReturn.length;

			for(I=0; I<J; I++)
				{
				var arraySelect=arrayReturn[I];
				arraySelect.style.fontWeight='bold';
				}

			var arrayReturn=document.getElementsByTagName('dd');
			J=arrayReturn.length;

			for(I=0; I<J; I++)
				{
				var arraySelect=arrayReturn[I];
				arraySelect.style.display='block';
				}
			}

		function closeECAll()
			{
			var arrayReturn=document.getElementsByTagName('dt');
			J=arrayReturn.length;

			for(I=0; I<J; I++)
				{
				var arraySelect=arrayReturn[I];
				arraySelect.style.fontWeight='normal';
				}

			var arrayReturn=document.getElementsByTagName('dd');
			J=arrayReturn.length;

			for(I=0; I<J; I++)
				{
				var arraySelect=arrayReturn[I];
				arraySelect.style.display='none';
				}
			}

		function openECList(dtElm)
			{
			if(dtElm.nextSibling && dtElm.nextSibling.tagName == 'DD')
				{
				dtElm.nextSibling.style.display='block';
				openECList(dtElm.nextSibling);
				}
			}

		function closeECList(dtElm)
			{
			if(dtElm.nextSibling && dtElm.nextSibling.tagName == 'DD')
				{
				dtElm.nextSibling.style.display='none';
				closeECList(dtElm.nextSibling);
				}
			}

		function toggleECList()
			{
			var arrayReturn=document.getElementsByTagName('dt');
			j=arrayReturn.length;

			for(i=0; i<j; i++)
				{
				var arraySelect=arrayReturn[i];
				arraySelect.onclick=function()
							{
							if(this.nextSibling.tagName == 'DD' && this.nextSibling.style.display == 'none')
								{
								openECList(this);
								this.style.fontWeight='bold';
								}
							else
								{
								closeECList(this);
								this.style.fontWeight='normal';
								}
							return false;
							}
				}
			}
		window.onload=toggleECList;

	// End SR Expand Collapse Script

