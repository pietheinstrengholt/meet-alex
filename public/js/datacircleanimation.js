function DataGrowthOnScroll() {

  var elementClientHeight = document.documentElement.clientHeight;
  var elementRect = document.getElementById("object_datacircles").getBoundingClientRect();
  var elementRect_y = elementRect.top - elementRect.height;
  //var scrollpercent = 1 * (elementClientHeight-elementRect.top)/elementRect.height;

  //var scrollpercent = (document.body.scrollTop + document.documentElement.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
  var scrollposition = document.body.scrollTop + document.documentElement.scrollTop;
  var scrollpercent = 100 * (((elementClientHeight + elementRect.height - elementRect.top)/elementRect.height) - 1);

  var row_datacircles = document.getElementById('content_datacircles');
  var row_datacircles_height = row_datacircles.offsetHeight;

  var object_datacircles = document.getElementById('object_datacircles');

  var datacircle1 = document.getElementById('datagrowthSVG1');
  var datacircle1scale = 0.2 * scrollpercent; if (datacircle1scale <= 0) { datacircle1scale = 0; }
  datacircle1.style.width = datacircle1scale + "%";
  datacircle1.style.clip = "rect(0px 100% " + row_datacircles_height + "px 0px)";

  var datacircle2 = document.getElementById('datagrowthSVG2');
  var datacircle2scale = 250-scrollpercent; if (datacircle2scale <= 0) { datacircle2scale = 0; }
  datacircle2.style.width = datacircle2scale + "%";
  object_datacircles.style.height = row_datacircles_height + "px";
  datacircle2.style.clip = "rect(0px 100% " + row_datacircles_height + "px 0px)";
  //console.log(scrollpercent, elementRect.top, elementRect.bottom, elementClientHeight);
}

// Find scroll percentage on scroll (using cross-browser properties), and offset dash same amount as percentage scrolled
window.addEventListener("scroll", DataGrowthOnScroll);
window.addEventListener("resize", DataGrowthOnScroll);
