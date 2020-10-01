
define([], function(){
    
    let categoryBanners = Array.from(document.querySelector(".category-banners-container").children);
    let eventTriggeredFlag = false;
    
    categoryBanners.forEach(banner => {
        banner.addEventListener ("mouseenter", () => {
            if (!eventTriggeredFlag) {
                eventTriggeredFlag = true;
                categoryBanners.forEach(otherBanner => {
                    otherBanner.className = "category-banner";
                });
               banner.className = "category-banner active";
                setTimeout(function(){ 
                   eventTriggeredFlag = false;
                }, 500);
            }
        });
    });
});

