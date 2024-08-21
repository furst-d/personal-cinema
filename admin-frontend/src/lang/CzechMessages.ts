import { TranslationMessages } from 'react-admin';
import czechMessages from 'ra-language-czech';

export const extendedCzechMessages: TranslationMessages = {
    ...czechMessages,
    ra: {
        ...czechMessages.ra,
        action: {
            ...czechMessages.ra.action,
            saved_queries: 'Uložené dotazy',
            clear_array_input: 'Vymazat pole',
            remove_all_filters: 'Odebrat všechny filtry',
            open: 'Otevřít',
            toggle_theme: 'Přepnout téma',
            update_application: 'Aktualizovat aplikaci',
            select_columns: 'Vybrat sloupce',

            clear_input_value: czechMessages.ra.action.clear_input_value,
            clone: czechMessages.ra.action.clone,
            confirm: czechMessages.ra.action.confirm,
            create: czechMessages.ra.action.create,
            delete: czechMessages.ra.action.delete,
            edit: czechMessages.ra.action.edit,
            list: czechMessages.ra.action.list,
            refresh: czechMessages.ra.action.refresh,
            remove: czechMessages.ra.action.remove,
            save: czechMessages.ra.action.save,
            search: czechMessages.ra.action.search,
            select_all: czechMessages.ra.action.select_all,
            show: czechMessages.ra.action.show,
            sort: czechMessages.ra.action.sort,
            undo: czechMessages.ra.action.undo,
            unselect: czechMessages.ra.action.unselect,
            expand: czechMessages.ra.action.expand,
            close: czechMessages.ra.action.close,
            open_menu: czechMessages.ra.action.open_menu,
            close_menu: czechMessages.ra.action.close_menu,
            move_up: czechMessages.ra.action.move_up,
            move_down: czechMessages.ra.action.move_down,
        },
        auth: {
            ...czechMessages.ra.auth,
        },
        boolean: {
            ...czechMessages.ra.boolean,
        },
        input: {
            ...czechMessages.ra.input,
        },
        message: {
            ...czechMessages.ra.message,
            auth_error: 'Chyba ověření',
            clear_array_input: 'Vymazat pole',
        },
        navigation: {
            ...czechMessages.ra.navigation,
            partial_page_range_info: 'Zobrazuje se %{offsetBegin}-%{offsetEnd} z %{total}',
            current_page: 'Strana %{page}',
            page: 'Strana',
            first: 'První',
            last: 'Poslední',
            previous: 'Předchozí',
        },
        notification: {
            ...czechMessages.ra.notification,
            application_update_available: 'Nová verze aplikace je k dispozici.',
        },
        page: {
            ...czechMessages.ra.page,
        },
        sort: {
            ...czechMessages.ra.sort,
        },
        validation: {
            ...czechMessages.ra.validation,
        },
        saved_queries: {
            label: 'Uložené dotazy',
            query_name: 'Název dotazu',
            new_label: 'Nový dotaz',
            new_dialog_title: 'Vytvořit nový dotaz',
            remove_label: 'Odebrat dotaz',
            remove_label_with_name: 'Odebrat dotaz "%{name}"',
            remove_dialog_title: 'Odebrat dotaz',
            remove_message: 'Opravdu chcete odebrat tento dotaz?',
            help: 'Nápověda',
        }
    },
};
