let headingAnchors = [];
$('.panel-heading').each(function(arg1, arg2) {
    headingAnchors.push($(arg2));
});

console.log(headingAnchors);
$(window).on('scroll', function() {
  let scrollTop = $(window).scrollTop() -200;
  let closestTopAnchor =  null ;
  for (let anchor of headingAnchors){
    if( scrollTop < anchor.offset().top && (!closestTopAnchor || anchor.offset().top < closestTopAnchor.offset().top)){
      closestTopAnchor = anchor;
    }
  }
  console.log();
  $('.navbar-nav li a').siblings('.arrow').removeClass('active');
  $('.navbar-nav  li a[href=#'+closestTopAnchor.attr('id')+'] ').siblings('.arrow').addClass('active');
});
