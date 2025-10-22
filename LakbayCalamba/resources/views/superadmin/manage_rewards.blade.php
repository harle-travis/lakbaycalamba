@ -183,7 +183,7 @@ Calamba Tourism Office"
                        <div id="emailPreview" class="bg-white border border-gray-300 rounded-lg p-4 min-h-64 max-h-96 overflow-y-auto">
                            <div class="text-center py-8">
                                <i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-500 text-sm">Select a user and click "Preview Email" to see how the email will look.</p>
                                <p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p>
                            </div>
                        </div>
                    </div>
@ -398,6 +398,22 @@ document.addEventListener('DOMContentLoaded', function() {
        emailEditor.classList.add('hidden');
    });

    // Edit email button functionality
    editEmailBtn.addEventListener('click', function() {
        const emailEditor = document.getElementById('emailEditor');
        if (emailEditor.classList.contains('hidden')) {
            emailEditor.classList.remove('hidden');
            editEmailBtn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i><span>Hide Editor</span>';
            editEmailBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            editEmailBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
        } else {
            emailEditor.classList.add('hidden');
            editEmailBtn.innerHTML = '<i data-lucide="edit" class="w-4 h-4"></i><span>Edit Email</span>';
            editEmailBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            editEmailBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    });

    // Toggle custom content editor
    useCustomContent.addEventListener('change', function() {
        if (this.checked) {
@ -406,24 +422,12 @@ document.addEventListener('DOMContentLoaded', function() {
            customContentEditor.classList.add('hidden');
        }
        // Clear preview when toggling
        emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Select a user and click "Preview Email" to see how the email will look.</p></div>';
        emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p></div>';
    });

    // Preview email functionality
    previewEmailBtn.addEventListener('click', function() {
        const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
        
        if (checkedUsers.length === 0) {
            alert('Please select at least one user to preview the email.');
            return;
        }

        if (checkedUsers.length > 1) {
            alert('Please select only one user to preview the email.');
            return;
        }

        const userId = checkedUsers[0].value;
        const subject = emailSubject.value;
        const content = emailContent.value;
        const useCustom = useCustomContent.checked;
@ -431,6 +435,18 @@ document.addEventListener('DOMContentLoaded', function() {
        // Show loading state
        emailPreview.innerHTML = '<div class="flex items-center justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600">Loading preview...</span></div>';

        // Use first available user for preview, or create a sample user
        let userId = null;
        if (checkedUsers.length > 0) {
            userId = checkedUsers[0].value;
        } else {
            // Use the first user from the list for preview
            const firstUserCheckbox = document.querySelector('.user-checkbox');
            if (firstUserCheckbox) {
                userId = firstUserCheckbox.value;
            }
        }

        // Make AJAX request to preview email
        fetch('{{ route("superadmin.preview-email") }}', {
            method: 'POST',
@ -475,7 +491,7 @@ document.addEventListener('DOMContentLoaded', function() {
            emailContent.value = '';
            useCustomContent.checked = false;
            customContentEditor.classList.add('hidden');
            emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Select a user and click "Preview Email" to see how the email will look.</p></div>';
            emailPreview.innerHTML = '<div class="text-center py-8"><i data-lucide="mail" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i><p class="text-gray-500 text-sm">Click "Preview Email" to see how the email will look.</p></div>';
        }
    });

@ -544,6 +560,18 @@ Calamba Tourism Office`;
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        console.log('Form checkboxes:', document.querySelectorAll('.user-checkbox').length);
        
        // Add form submission handler for debugging
        form.addEventListener('submit', function(e) {
            const checkedUsers = document.querySelectorAll('.user-checkbox:checked');
            console.log('Form submitting with', checkedUsers.length, 'users selected');
            
            if (checkedUsers.length === 0) {
                e.preventDefault();
                alert('Please select at least one user to send notifications to.');
                return false;
            }
        });
    } else {
        console.error('Form not found!');
    }
