<script setup>
const model = defineModel({ type: String, required: true });
</script>

<template>
  <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm mt-4">
    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-3">
      {{ $t('admin.map_style') }}
    </label>

    <div class="relative">
      <select
        v-model="model"
        class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer hover:bg-slate-100/50"
        style="
          background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2220%22%20height%3D%2220%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22/%3E%3C/svg%3E');
          background-repeat: no-repeat;
          background-position: right 12px center;
          background-size: 16px;
        "
      >
        <option value="osm">{{ $t('admin.style_osm') }}</option>
        <option value="satellite">{{ $t('admin.style_satellite') }}</option>
      </select>
    </div>
  </div>
</template>
