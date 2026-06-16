import { useState, useRef, useEffect } from 'react';

export default function SearchSelect({ value, onChange, options, placeholder, labelKey, valueKey, className }) {
    const [search, setSearch] = useState('');
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    const selected = options.find((o) => String(o[valueKey]) === String(value));

    useEffect(() => {
        const handler = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false); };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    const filtered = options.filter((o) => {
        if (!search) return true;
        const label = String(o[labelKey] ?? '').toLowerCase();
        return label.includes(search.toLowerCase());
    });

    const handleSelect = (opt) => {
        onChange(String(opt[valueKey]));
        setSearch(String(opt[labelKey] ?? ''));
        setOpen(false);
    };

    useEffect(() => {
        if (!open) setSearch(selected ? String(selected[labelKey] ?? '') : '');
    }, [open]);

    return (
        <div ref={ref} className={`relative ${className ?? ''}`}>
            <input
                type="text"
                value={open ? search : (selected ? String(selected[labelKey] ?? '') : '')}
                placeholder={placeholder}
                onFocus={() => { setOpen(true); setSearch(''); }}
                onChange={(e) => { setOpen(true); setSearch(e.target.value); }}
                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            {open && (
                <div className="absolute z-50 mt-1 max-h-48 w-full overflow-y-auto rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 shadow-lg">
                    <div
                        className="px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                        onClick={() => { onChange(''); setSearch(''); setOpen(false); }}
                    >
                        {placeholder}
                    </div>
                    {filtered.map((opt) => (
                        <div
                            key={opt[valueKey]}
                            className={`px-3 py-1.5 text-sm cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/20 ${String(opt[valueKey]) === String(value) ? 'bg-indigo-100 dark:bg-indigo-900/30 font-medium' : ''}`}
                            onClick={() => handleSelect(opt)}
                        >
                            {opt[labelKey]}
                        </div>
                    ))}
                    {filtered.length === 0 && (
                        <div className="px-3 py-1.5 text-sm text-gray-400">Sin resultados</div>
                    )}
                </div>
            )}
        </div>
    );
}
