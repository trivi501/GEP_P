import { router, usePage } from '@inertiajs/react';
import { useEffect, useState, useRef, useCallback } from 'react';
import Dropdown from '@/Components/Dropdown';

export default function NotificationBell() {
    const [notifications, setNotifications] = useState([]);
    const [count, setCount] = useState(0);
    const [show, setShow] = useState(false);
    const intervalRef = useRef(null);
    const dropdownRef = useRef(null);

    const fetchNotifications = useCallback(async () => {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(route('notifications.index'), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            });
            if (res.ok) {
                const json = await res.json();
                setNotifications(json.unread);
                setCount(json.count);
            }
        } catch (e) {
            // silent
        }
    }, []);

    useEffect(() => {
        fetchNotifications();
        intervalRef.current = setInterval(fetchNotifications, 5000);
        return () => { if (intervalRef.current) clearInterval(intervalRef.current); };
    }, [fetchNotifications]);

    const markAsRead = async (notification) => {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(route('notifications.read', notification.id), {
                method: 'PUT',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            });
            setNotifications(prev => prev.filter(n => n.id !== notification.id));
            setCount(prev => Math.max(0, prev - 1));
            if (notification.ticket_id) {
                router.visit(route('support-tickets.show', notification.ticket_id));
            }
        } catch (e) {
            // silent
        }
    };

    const handleToggle = () => setShow(prev => !prev);

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setShow(false);
            }
        };
        if (show) {
            document.addEventListener('mousedown', handleClickOutside);
        }
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [show]);

    return (
        <div className="relative" ref={dropdownRef}>
            <button
                type="button"
                onClick={handleToggle}
                className="relative inline-flex items-center rounded-md p-2 text-gray-500 transition hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200"
                title="Notificaciones"
            >
                <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                {count > 0 && (
                    <span className="absolute -right-1 -top-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                        {count > 9 ? '9+' : count}
                    </span>
                )}
            </button>

            {show && (
                <div className="absolute right-0 z-50 mt-2 w-80 rounded-md bg-white shadow-lg ring-1 ring-black/5 dark:bg-gray-800 dark:ring-gray-600">
                    <div className="border-b border-gray-200 px-4 py-2 dark:border-gray-600">
                        <h4 className="text-sm font-semibold text-gray-700 dark:text-gray-200">Notificaciones</h4>
                    </div>
                    <div className="max-h-64 overflow-y-auto">
                        {notifications.length === 0 ? (
                            <p className="px-4 py-6 text-center text-sm text-gray-500">Sin notificaciones nuevas</p>
                        ) : (
                            notifications.map((n) => (
                                <button
                                    key={n.id}
                                    type="button"
                                    onClick={() => markAsRead(n)}
                                    className="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                >
                                    <p className="text-gray-800 dark:text-gray-200">{n.message}</p>
                                    <p className="mt-0.5 text-xs text-gray-400">{n.created_at}</p>
                                </button>
                            ))
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}
