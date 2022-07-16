
<div class="favorites">

<?= $this->render('//product/favorites-items',compact('model','pages','products','page_status')) ?>

</div>


<script>

  let page=getParameterByName('page');

  let favorites=document.querySelector('.favorites');

  favorites.onclick=function(e){
    if(e.target.tagName=='I' && e.target.classList.contains('product-favorites')){
            $.ajax({
              url:window.location.origin+'/product/favorites',
              method:'GET',
              data: { product_id:e.target.getAttribute('data-id'), page:page },
              success: function(res) { favorites.innerHTML=res;
              console.log(favorites);
              let page_favorites=favorites.querySelector('.page-favorites').innerHTML;
              if(page_favorites=='prev') prevPage();
              if(page_favorites=='current') window.location=window.location.href;
              
               }
            });
    }
  }


 function prevPage()
 {
  let currentSearch=window.location.search;
  let new_search='';
  let currentPage=getParameterByName('page');
  currentPage=parseInt(currentPage)-1;
  if(currentSearch.includes('?page')) new_search=currentSearch.replace(/\?page=\d+/,'?page='+currentPage);
  if(currentSearch.includes('&page')) new_search=currentSearch.replace(/\&page=\d+/,'&page='+currentPage);
  let new_location=window.location.origin+window.location.pathname+new_search;
  window.location=new_location;
 }



  function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

</script>