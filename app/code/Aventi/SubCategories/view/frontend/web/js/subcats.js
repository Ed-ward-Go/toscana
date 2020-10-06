
define([], function(){
    
    let elementContainer = document.querySelector(".subcats__container__content");
    let prevButton = document.querySelector("#prev");
    let nextButton = document.querySelector("#next");
    let subCategories = Array.from(elementContainer.children);
    let maximumReached = false;
    let opacityButtons = '0.6';

    nextButton.addEventListener("click", () => {
        console.log(maximumReached)
        prevButton.style.opacity = '1';
        if (maximumReached == false) {
            subCategories.forEach(element => {
                

                let style = element.currentStyle || window.getComputedStyle(element);

                let currentMarginRight = parseInt(style.marginRight.substring(0, style.marginRight.length - 2 ));
                let currentMarginLeft = parseInt(style.marginLeft.substring(0, style.marginRight.length - 2 ));
                let marginSums = currentMarginLeft + currentMarginRight;
                
                let lastTranslate = getTranslateX(element);
                let currentTranslate = lastTranslate - (element.offsetWidth + marginSums );
        
                
                let totalElementWidth = (subCategories.length * element.offsetWidth);
                let maxElementsinRow = (( Math.trunc(elementContainer.offsetWidth / element.offsetWidth) ) - 2  );
                let maxWidthinRow = element.offsetWidth * maxElementsinRow;
        
                if ( Math.abs(currentTranslate) < totalElementWidth - maxWidthinRow ) {   
                    element.style.transform = "translateX(" + (currentTranslate) + "px)";
                } else {
                    maximumReached = true;
                    nextButton.style.opacity = opacityButtons;
                }
            });
        } else {
            subCategories.forEach(element => {
                element.style.transform = "translateX(" + (0) + "px)";
                prevButton.style.opacity = opacityButtons;
                nextButton.style.opacity = '1';
                maximumReached = false;
            });
        }

    })

    prevButton.addEventListener("click", () => {
        prevButton.style.opacity = '1';
            subCategories.forEach(element => {
                let style = element.currentStyle || window.getComputedStyle(element);
                
                let currentMarginRight = parseInt(style.marginRight.substring(0, style.marginRight.length - 2 ));
                let currentMarginLeft = parseInt(style.marginLeft.substring(0, style.marginRight.length - 2 ));
                let marginSums = currentMarginLeft + currentMarginRight;
                
                let lastTranslate = getTranslateX(element);
                let currentTranslate = lastTranslate + (element.offsetWidth + marginSums);

                if ( currentTranslate < 0) {
                    element.style.transform = "translateX(" + (currentTranslate) + "px)";
                } else {
                    element.style.transform = "translateX(" + (0) + "px)";
                }
            });
    })

    nextButton.style.opacity = '1';
    prevButton.style.opacity = opacityButtons;

    function getTranslateX(element) {
        var style = window.getComputedStyle(element);
        var matrix = new WebKitCSSMatrix(style.webkitTransform);
        return matrix.m41;
    }
});

