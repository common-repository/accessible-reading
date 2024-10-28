/*!
    Name: accessible-reading.js
    Author: AuRise Creative | https://aurisecreative.com
    Last Modified: October 9, 2022 @ 14:24
*/
if (!!document.querySelector('.accessible-reading-toggle')) {
    var AU_AccessibleReading = {
        Init: function() {
            let checkboxes = document.querySelectorAll('.accessible-reading-toggle input[type="checkbox"]');
            if (!!checkboxes) {
                let cookie = AU_AccessibleReading.GetData('AU_AccessibleReading');
                for (let checkbox of checkboxes) {
                    checkbox.addEventListener('click', AU_AccessibleReading.ToggleHandler);
                    if (cookie == 'enabled' || (cookie != 'disabled' && window.AU_AccessibleReading_Default == 1)) {
                        AU_AccessibleReading.ToggleAccessibleReading(true);
                        checkbox.setAttribute('checked', 'checked');
                    }
                }
            }
            //console.info('Accessible Reading initialisation complete');
        },
        ToggleAccessibleReading: function(toggle) {
            for (let content of document.querySelectorAll('.accessible-reading-original-content')) {
                if (toggle) {
                    content.setAttribute('style', 'display:none');
                } else {
                    content.setAttribute('style', 'display:block');
                }
            }
            for (let content of document.querySelectorAll('.accessible-reading-bionic-content')) {
                if (toggle) {
                    content.setAttribute('style', 'display:block');
                } else {
                    content.setAttribute('style', 'display:none');
                }
            }
        },
        ToggleHandler: function(e) {
            AU_AccessibleReading.ToggleAccessibleReading(e.target.checked);
            //Check if there are others that need to be toggled
            let others = document.querySelectorAll('.accessible-reading-toggle input[type="checkbox"]:not(#' + e.target.getAttribute('id') + ')');
            if (!!others) {
                for (let checkbox of others) {
                    if (e.target.checked !== checkbox.checked) {
                        checkbox.checked = e.target.checked;
                    }
                }
            }
        },
        GetData: function(key) {
            var value = AU_AccessibleReading.GetCookie(key);
            //If the value is still null, check session storage
            if (value === null) {
                value = AU_AccessibleReading.GetSessionStorage(key);
            }
            return value;
        },
        SetData: function(key, value, days, disable_local) {
            var return_msg = {
                'status': 'Setting Cookie',
                'exists': AU_AccessibleReading.GetCookie(key) !== null,
                'storage': 'cookie'
            };
            if (return_msg.storage == 'cookie') {
                var attempt = AU_AccessibleReading.SetCookie(key, value, days);
                return_msg.status = attempt.status;
                return_msg.exists = attempt.exists;
                //If the cookie attempt failed, use a fallback method
                if (!attempt.exists) {
                    if (days > 0 || disable_local) {
                        //Fallback to session storage because this cookie is supposed to expire
                        return_msg.storage = 'session';
                    } else {
                        //Fallback to local storage since this cookie is not meant to expire anytime soon
                        return_msg.storage = 'local';
                    }
                }
            }
            if (return_msg.storage == 'session') {
                return_msg.exists = AU_AccessibleReading.SetSessionStorage(key, value);
                return_msg.status = 'Utility: Data ' + (return_msg.exists !== null ? ' was successfully' : 'FAILED to be') + ' saved into session storage: ' + key;
            }
            return return_msg;
        },
        DeleteData: function(key) {
            AU_AccessibleReading.DeleteCookie(key);
            AU_AccessibleReading.DeleteSessionStorage(key);
        },
        GetCookie: function(key) {
            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : null;
        },
        SetCookie: function(key, value, days) {
            var return_msg = {
                    'status': 'Getting Cookie',
                    'exists': AU_AccessibleReading.GetCookie(key) !== null
                },
                expires = '',
                data = value;
            if (days == 'delete') {
                //If deleting the cookie, remove the data and set the expiration date to sometime in the distant past
                data = '';
                expires = '; expires=Thu, 01-Jan-70 00:00:01 GMT';
            } else {
                if (typeof(days) != 'number' || days <= 0) {
                    days = 73000; //This is intended to never expire, so set for 200 years from now
                }
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            if (typeof(data) == 'object') {
                data = JSON.stringify(data); //Stringify the object using JSON syntax
            }
            var cookie_string = key + "=" + data + expires + "; Secure; SameSite=Lax; path=/";
            document.cookie = cookie_string; //Set the cookie
            //Check to see if the cookie was successfully created
            return_msg.status = 'Utility: Cookie was successfully set: ' + key;
            return_msg.exists = AU_AccessibleReading.GetCookie(key) !== null;
            if (!return_msg.exists) {
                return_msg.status = 'Utility: Cookie FAILED to be created: ' + key;
                if (days == 'delete') {
                    return_msg.status = 'Utility: Cookie was successfully deleted (content set to empty string, and it expires in the past): ' + key;
                }
            }
            return return_msg;
        },
        DeleteCookie: function(key) {
            return AU_AccessibleReading.SetCookie(key, undefined, 'delete');
        },
        GetSessionStorage: function(key) {
            if (typeof(Storage) !== undefined) {
                var value = sessionStorage.getItem(key);
                return value;
            }
            return null;
        },
        SetSessionStorage: function(key, value) {
            if (typeof(Storage) !== undefined) {
                var data = value;
                if (typeof(data) == 'object') { data = JSON.stringify(data); }
                sessionStorage.setItem(key, data);
                return AU_AccessibleReading.GetSessionStorage(key);
            }
            return null;
        },
        DeleteSessionStorage: function(key) {
            if (typeof(Storage) !== undefined) {
                sessionStorage.removeItem(key);
            }
        }
    };
    AU_AccessibleReading.Init();
}