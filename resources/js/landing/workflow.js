/**
 * NetFly - Landing Page Sticky Scrollytelling Workflow Module
 */

export function initWorkflowScrollytelling() {
    const scrollyContainer = document.getElementById('workflowScrollytelling');
    if (!scrollyContainer) return;

    const stages = document.querySelectorAll('.workflow-stage-data');
    const visualCards = document.querySelectorAll('.workflow-visual-card');
    const stepTabs = document.querySelectorAll('.workflow-step-tab');
    const progressFill = document.getElementById('workflowProgressFill');
    const stepCounterText = document.getElementById('workflowStepCounterText');

    if (stages.length === 0 || visualCards.length === 0) return;

    let activeIndex = -1;
    let isTicking = false;

    function setActiveStage(index) {
        if (index === activeIndex) return;
        activeIndex = index;

        // 1. Update Left Narrative Stages (CSS Grid Stack Smooth Gliding & Fade)
        stages.forEach((stage, i) => {
            if (i === index) {
                stage.classList.remove('opacity-0', 'translate-y-8', 'scale-95', 'pointer-events-none', 'z-0');
                stage.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto', 'z-10');
            } else {
                stage.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto', 'z-10');
                stage.classList.add('opacity-0', 'translate-y-8', 'scale-95', 'pointer-events-none', 'z-0');
            }
        });

        // 2. Update Right Visual Cards (CSS Grid Stack Smooth Scale, Blur & Fade)
        visualCards.forEach((card, i) => {
            if (i === index) {
                card.classList.remove('opacity-0', 'translate-y-6', 'scale-95', 'pointer-events-none', 'z-0', 'blur-[2px]');
                card.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto', 'z-10', 'blur-0');
            } else {
                card.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto', 'z-10', 'blur-0');
                card.classList.add('opacity-0', 'translate-y-6', 'scale-95', 'pointer-events-none', 'z-0', 'blur-[2px]');
            }
        });

        // 3. Update Step Tabs
        stepTabs.forEach((tab, i) => {
            if (i === index) {
                tab.className =
                    'workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-blue-600 text-white shadow-md shadow-blue-500/20';
            } else if (i < index) {
                tab.className =
                    'workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else {
                tab.className =
                    'workflow-step-tab shrink-0 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-slate-600 border border-slate-200 hover:bg-slate-100';
            }
        });

        // 4. Update Progress Bar & Counter
        if (progressFill) {
            const pct = Math.round(((index + 1) / stages.length) * 100);
            progressFill.style.width = `${pct}%`;
        }
        if (stepCounterText) {
            stepCounterText.textContent = `Tahap ${index + 1} dari ${stages.length}`;
        }
    }

    function handleScroll() {
        if (!isTicking) {
            requestAnimationFrame(() => {
                const rect = scrollyContainer.getBoundingClientRect();
                const containerHeight = scrollyContainer.offsetHeight;
                const windowHeight = window.innerHeight;
                const totalScrollable = containerHeight - windowHeight;

                if (totalScrollable > 0) {
                    const scrolledDistance = -rect.top;
                    const progress = Math.max(0, Math.min(1, scrolledDistance / totalScrollable));
                    const stageIndex = Math.min(
                        stages.length - 1,
                        Math.floor(progress * stages.length)
                    );
                    setActiveStage(stageIndex);
                }

                isTicking = false;
            });
            isTicking = true;
        }
    }

    // Attach scroll and resize listeners with passive optimization
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleScroll, { passive: true });

    // Initial setup
    setActiveStage(0);

    // Interactive Step Tab click listener
    stepTabs.forEach((tab, targetIndex) => {
        tab.addEventListener('click', () => {
            const containerTop = scrollyContainer.getBoundingClientRect().top + window.pageYOffset;
            const containerHeight = scrollyContainer.offsetHeight;
            const windowHeight = window.innerHeight;
            const totalScrollable = containerHeight - windowHeight;

            const targetY = containerTop + (targetIndex / stages.length) * totalScrollable + 5;

            window.scrollTo({
                top: targetY,
                behavior: 'smooth',
            });

            setActiveStage(targetIndex);
        });
    });
}
