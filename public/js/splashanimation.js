function SplashAnimationOnScroll() {

  // Get the splash_svg_object by ID
	var svgObj = document.getElementById("splash_svg_object");
	// Get the SVG document inside the Object tag
	var svgDoc = svgObj.contentDocument;
	// Get one of the SVG elements by ID;

  //var scrollpercent = (document.body.scrollTop + document.documentElement.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
  var scrollposition = document.body.scrollTop + document.documentElement.scrollTop;

  // get and determine the position of each SVG object for animation on scroll

  //////////////////////////////////////////////////////////////////////////////////////////
    // Collection A
    var pyramidA = svgDoc.getElementById('splash_pyramidA');
    var pyramidA_position = {
          x: 120 - 50 * scrollposition
    }; //if (pyramidA_position.x < 10) { pyramidA_position.x = 10; }
		//if (scrollposition > 5) { pyramidA.style.animationPlayState = "paused"; pyramidA.style.WebkitAnimationPlayState = "paused"; pyramidA.setAttribute('x', "10"); }
		//pyramidA.setAttribute('x', pyramidA_position.x);

    // termA1
    var termA1 = svgDoc.getElementById('splash_termA1');
    var termA1_visibility = "hidden";
    if (scrollposition > 20) { termA1_visibility = "visible"; } else { termA1_visibility = "hidden"; };
    termA1.style.visibility = termA1_visibility;

    // termA2
    var termA2 = svgDoc.getElementById('splash_termA2');
    var termA2_visibility = "hidden";
    if (scrollposition > 50) { termA2_visibility = "visible"; } else { termA2_visibility = "hidden"; };
    termA2.style.visibility = termA2_visibility;

    // relation between termA1 and termA2
		var relationlineA = svgDoc.getElementById('splash_line_relationA');
		var relationlineA_visibility = "hidden";
		var relationlineA_position = {
	        y2: 200 - 1 * scrollposition
	  };
		if (relationlineA_position.y2 < 85) { relationlineA_position.y2 = 85; }
		if (relationlineA_position.y2 < 135) { relationlineA_visibility = "visible"; } else { relationlineA_visibility = "hidden"; };
		relationlineA.setAttribute('y2', relationlineA_position.y2);
		relationlineA.style.visibility = relationlineA_visibility;

		var relationboxA = svgDoc.getElementById('splash_relationA');
		var relationboxA_visibility = "hidden";
		if (scrollposition > 95) { relationboxA_visibility = "visible"; } else { relationboxA_visibility = "hidden"; };
		relationboxA.style.visibility = relationboxA_visibility;

  //////////////////////////////////////////////////////////////////////////////////////////

    // Collection B
    var pyramidB = svgDoc.getElementById('splash_pyramidB');
    var pyramidB_position = {
				x: 120 + 50 * scrollposition
		}; if (pyramidB_position.x > 250) { pyramidB_position.x = 250; }
    //pyramidB.setAttribute('x', pyramidB_position.x);

    // termB1
    var termB1 = svgDoc.getElementById('splash_termB1');
    var termB1_visibility = "hidden";
    if (scrollposition > 15) { termB1_visibility = "visible"; } else { termB1_visibility = "hidden"; };
    termB1.style.visibility = termB1_visibility;

    // termB2
    var termB2 = svgDoc.getElementById('splash_termB2');
    var termB2_visibility = "hidden";
    if (scrollposition > 45) { termB2_visibility = "visible"; } else { termB2_visibility = "hidden"; };
    termB2.style.visibility = termB2_visibility;

    // relation between termB1 and termB2
    var relationB = svgDoc.getElementById('splash_relationB');
    var relationB_visibility = "hidden";
    if (scrollposition > 85) { relationB_visibility = "visible"; } else { relationB_visibility = "hidden"; };
    relationB.style.visibility = relationB_visibility;

		/////

		var relationlineB = svgDoc.getElementById('splash_line_relationB');
		var relationlineB_visibility = "hidden";
		var relationlineB_position = {
	        y2: 200 - 1 * scrollposition
	  };
		if (relationlineB_position.y2 < 85) { relationlineB_position.y2 = 85; }
		if (relationlineB_position.y2 < 135) { relationlineB_visibility = "visible"; } else { relationlineB_visibility = "hidden"; };
		relationlineB.setAttribute('y2', relationlineB_position.y2);
		relationlineB.style.visibility = relationlineB_visibility;

		var relationboxB = svgDoc.getElementById('splash_relationB');
		var relationboxB_visibility = "hidden";
		if (scrollposition > 95) { relationboxB_visibility = "visible"; } else { relationboxB_visibility = "hidden"; };
		relationboxB.style.visibility = relationboxB_visibility;

  //////////////////////////////////////////////////////////////////////////////////////////

  // Cross-pyramid relation
  var relationAB = svgDoc.getElementById('splash_line_relationAB');
  var relationAB_position = {
        x1: 250,
        x2: 0 + 1 * scrollposition
  }; if (relationAB_position.x2 > 265) { relationAB_position.x2 = 265; }
  var relationAB_visibility = "hidden";
  if (scrollposition > 90) { relationAB_visibility = "visible"; } else { relationAB_visibility = "hidden"; };
  relationAB.style.visibility = relationAB_visibility;
  relationAB.setAttribute('x2', relationAB_position.x2);

  var relationboxAB = svgDoc.getElementById('splash_relationboxAB');
  var relationboxAB_visibility = "hidden";
  if (scrollposition > 180) { relationboxAB_visibility = "visible"; } else { relationboxAB_visibility = "hidden"; };
  relationboxAB.style.visibility = relationboxAB_visibility;

  //////////////////////////////////////////////////////////////////////////////////////////

  // Tagline
  var tagline = svgDoc.getElementById('splash_tagline');
  var tagline_visibility = "hidden";
  if (scrollposition > 200) { tagline_visibility = "visible"; } else { tagline_visibility = "hidden"; };
  tagline.style.visibility = tagline_visibility;

}

// Find scroll percentage on scroll (using cross-browser properties), and offset dash same amount as percentage scrolled
window.addEventListener("scroll", SplashAnimationOnScroll);
