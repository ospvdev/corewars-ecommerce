import { reactive, computed, watchEffect, type CSSProperties, ref, onMounted, onUnmounted, type Ref } from 'vue';

export function useMouse(): { updateMousePosition: (e: MouseEvent) => void; pointStyle: Ref<CSSProperties> ; state: { mouseX: number; mouseY: number; showPoint: boolean; }; } {
  const x = ref(0);
  const y = ref(0);

  function update(e: MouseEvent) {
    x.value = e.pageX;
    y.value = e.pageY;
  }

  onMounted(() => window.addEventListener('mousemove', update));
  onUnmounted(() => window.removeEventListener('mousemove', update));

  const trackerRef = ref<HTMLElement|null>(null);
  type State = { mouseX: number; mouseY: number; followerX: number; followerY: number; showPoint: boolean; };
  const state = reactive<State>({ mouseX: 0, mouseY: 0, followerX: 0, followerY: 0, showPoint: false });

  watchEffect(() => {
    const rect = trackerRef.value?.getBoundingClientRect();

    if (!rect) return;

    state.mouseX = x.value - (rect.left + window.scrollX);
    state.mouseY = y.value - (rect.top + window.scrollY);
    state.showPoint = true;
  });

  const updateMousePosition = (e: MouseEvent) => {
    const el = e.currentTarget as HTMLElement;
    const rect = el.getBoundingClientRect();
    
    state.mouseX = e.clientX - rect.left;
    state.mouseY = e.clientY - rect.top;
    state.showPoint = true;
  };

  // handleLeaveContainer was removed because it wasn't used; leaving logic centralized in other handlers

  const handleDocMouseOut = (e: MouseEvent) => {
    if (e.relatedTarget === null) state.showPoint = false;
  };

  const handleBlur = () => (state.showPoint = false);
  const handleVisibility = () => { if (document.hidden) state.showPoint = false; };

  let raf: number;
  const animate = () => {
    const speed = 0.05;
    state.followerX += (state.mouseX - state.followerX) * speed;
    state.followerY += (state.mouseY - state.followerY) * speed;

    raf = requestAnimationFrame(animate);
  }

  onMounted(() => {
    raf = requestAnimationFrame(animate);

    document.addEventListener('mouseout', handleDocMouseOut);
    window.addEventListener('blur', handleBlur);
    document.addEventListener('visibilitychange', handleVisibility);
  });

  onUnmounted(() => {
    cancelAnimationFrame(raf);

    document.removeEventListener('mouseout', handleDocMouseOut);
    window.removeEventListener('blur', handleBlur);
    document.removeEventListener('visibilitychange', handleVisibility);
  });

  const pointStyle = computed<CSSProperties>(() => ({
    position: 'absolute',
    top: `${state.followerY}px`,
    left: `${state.followerX}px`,
    width: '10px',
    height: '10px',
    backgroundColor: '#46e99e',
    borderRadius: '50%',
    pointerEvents: 'none',
    transform: 'translate(-50%, -50%)',
    opacity: state.showPoint ? 1 : 0, // para un fade suave
  }));
  
  return { updateMousePosition, pointStyle, state };
}