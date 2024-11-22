document.addEventListener("DOMContentLoaded", function() {
    const boxes = document.querySelectorAll('.box1-content');

    boxes.forEach(function(box) {
        let tooltip;

        box.addEventListener('mouseenter', function() {
            // Get the description from the data attribute and parse it as JSON
            const description = JSON.parse(box.getAttribute('data-description'));

            // Create tooltip element
            tooltip = document.createElement('div');
            tooltip.className = 'tooltips';
            
            // Set the innerHTML to render HTML content
            tooltip.innerHTML = description;
            
            // Append to body and position it
            document.body.appendChild(tooltip);
            const boxRect = box.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            // Position tooltip on the left side
            tooltip.style.top = (boxRect.top - tooltipRect.height - 10) + 'px';
            tooltip.style.left = (boxRect.left - tooltipRect.width - 10) + 'px';
            tooltip.style.display = 'block';

            // Prevent the tooltip from disappearing when mouse is over it
            tooltip.addEventListener('mouseenter', function() {
                tooltip.classList.add('hovered');
            });
            tooltip.addEventListener('mouseleave', function() {
                tooltip.classList.remove('hovered');
                tooltip.remove();
            });
        });

        box.addEventListener('mouseleave', function() {
            // Remove tooltip only if the mouse is not over the tooltip
            setTimeout(function() {
                if (!tooltip.classList.contains('hovered')) {
                    tooltip.remove();
                }
            }, 100);
        });
    });

    const scrollableContent = document.getElementById('scrollable-content');
    const scrollbarThumb = document.getElementById('scrollbar-thumb');
    const customScrollbar = document.querySelector('.custom-scrollbar');

    if (!scrollableContent || !scrollbarThumb || !customScrollbar) {
        console.error('Elements not found');
        return;
    }

    function updateThumbPosition() {
        const scrollRatio = scrollableContent.scrollLeft / (scrollableContent.scrollWidth - scrollableContent.clientWidth);
        scrollbarThumb.style.left = scrollRatio * (customScrollbar.clientWidth - scrollbarThumb.clientWidth) + 'px';
    }

    function toggleScrollbarVisibility() {
        if (scrollableContent.scrollWidth > scrollableContent.clientWidth) {
            customScrollbar.style.display = 'block';
        } else {
            customScrollbar.style.display = 'none';
        }
    }

    toggleScrollbarVisibility();
    window.addEventListener('resize', toggleScrollbarVisibility);
    scrollableContent.addEventListener('scroll', updateThumbPosition);

    let isDragging = false;
    let startX;

    scrollbarThumb.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.clientX - scrollbarThumb.offsetLeft;
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const thumbPosition = e.clientX - startX;
        const maxThumbPosition = customScrollbar.clientWidth - scrollbarThumb.clientWidth;
        const clampedPosition = Math.max(0, Math.min(thumbPosition, maxThumbPosition));
        
        scrollbarThumb.style.left = clampedPosition + 'px';

        const scrollRatio = clampedPosition / maxThumbPosition;
        scrollableContent.scrollLeft = scrollRatio * (scrollableContent.scrollWidth - scrollableContent.clientWidth);
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
    });


});