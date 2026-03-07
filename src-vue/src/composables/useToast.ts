import { ref } from 'vue';

const message = ref<string | null>(null);
let timer: ReturnType<typeof setTimeout> | null = null;

export function useToast() {
  const show = (msg: string, duration = 2500) => {
    if (timer) clearTimeout(timer);
    message.value = msg;
    timer = setTimeout(() => {
      message.value = null;
    }, duration);
  };

  return { message, show };
}
