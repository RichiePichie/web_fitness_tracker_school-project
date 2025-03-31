// Character Password Animation
document.addEventListener('DOMContentLoaded', function() {
    // Find login/register forms
    const authForms = document.querySelectorAll('.auth-form form');
    
    authForms.forEach(form => {
        // Get all input fields except submit/button/hidden
        const inputFields = form.querySelectorAll('input:not([type="submit"]):not([type="button"]):not([type="hidden"])');
        
        // Create character container to be placed at the top of the form
        const characterContainer = document.createElement('div');
        characterContainer.className = 'avatar-character';
        
        // Create character face
        const characterFace = document.createElement('div');
        characterFace.className = 'avatar-face';
        characterContainer.appendChild(characterFace);
        
        // Create eyes
        const leftEye = document.createElement('div');
        leftEye.className = 'avatar-eye left';
        characterFace.appendChild(leftEye);
        
        const rightEye = document.createElement('div');
        rightEye.className = 'avatar-eye right';
        characterFace.appendChild(rightEye);
        
        // Create mouth
        const mouth = document.createElement('div');
        mouth.className = 'avatar-mouth';
        characterFace.appendChild(mouth);
        
        // Insert the character at the beginning of the form, before the first element
        form.insertBefore(characterContainer, form.firstChild);
        
        // Character properties
        let eyeBasePosition = { x: 0, y: 4 };
        let lastInputLength = 0;
        let activeInput = null;
        
        // Calculate the position for eyes based on typing
        const calculateEyePosition = () => {
            if (!activeInput) return;
            
            const inputRect = activeInput.getBoundingClientRect();
            const characterRect = characterContainer.getBoundingClientRect();
            
            // Base position calculations
            const centerX = characterRect.left + (characterRect.width / 2);
            const inputCenterX = inputRect.left + (inputRect.width / 2);
            const diffX = inputCenterX - centerX;
            
            // Set base looking position (just looking at the field)
            eyeBasePosition = {
                x: Math.max(-3, Math.min(3, diffX / 30)),
                y: 4 // Default downward looking position
            };
            
            updateEyePosition();
        };
        
        // Update eye position based on cursor/text position
        const updateEyePosition = () => {
            // If no active input, reset to neutral
            if (!activeInput) {
                leftEye.style.transform = 'translate(0, 0)';
                rightEye.style.transform = 'translate(0, 0)';
                return;
            }
            
            const inputLength = activeInput.value.length;
            
            // Calculate position based on input length
            let progress = inputLength > 0 ? inputLength / 20 : 0; // Assuming max reasonable length is 20
            progress = Math.min(1, progress); // Cap at 1
            
            // Base movement + character following movement
            let moveX = eyeBasePosition.x;
            let moveY = eyeBasePosition.y;
            
            // Only add character following if there are characters
            if (inputLength > 0) {
                // Add horizontal movement based on text length
                // This simulates eyes moving right as more characters are typed
                const horizontalOffset = (progress * 6) - 3; // Range from -3 to 3
                moveX = eyeBasePosition.x + horizontalOffset;
                
                // Vertical adjustment - eyes look lower as input gets longer
                const verticalOffset = Math.min(2, inputLength / 10);
                moveY = eyeBasePosition.y + verticalOffset;
                
                // Add a small bounce effect when a new character is added
                if (inputLength > lastInputLength) {
                    characterContainer.classList.add('character-react');
                    setTimeout(() => {
                        characterContainer.classList.remove('character-react');
                    }, 150);
                }
            }
            
            // Limit movement range
            moveX = Math.max(-5, Math.min(5, moveX));
            moveY = Math.max(0, Math.min(8, moveY));
            
            // Apply the movement
            leftEye.style.transform = `translate(${moveX}px, ${moveY}px)`;
            rightEye.style.transform = `translate(${moveX}px, ${moveY}px)`;
            
            // Update last input length
            lastInputLength = inputLength;
        };
        
        // Listen for cursor position changes (via selection changes)
        const handleSelectionChange = () => {
            if (document.activeElement === activeInput) {
                updateEyePosition();
            }
        };
        
        // Add event listeners to all input fields
        inputFields.forEach(field => {
            field.addEventListener('focus', function() {
                // Set active input
                activeInput = this;
                // Calculate position for focused input
                calculateEyePosition();
                
                // Set watching state
                characterContainer.classList.add('watching');
                
                // Different expressions for different field types
                characterContainer.classList.remove('email-mode', 'password-mode', 'text-mode');
                if (this.type === 'email') {
                    characterContainer.classList.add('email-mode');
                } else if (this.type === 'password') {
                    characterContainer.classList.add('password-mode');
                } else {
                    characterContainer.classList.add('text-mode');
                }
            });
            
            field.addEventListener('blur', function() {
                // If this is the active input, reset
                if (activeInput === this) {
                    // Reset active input
                    activeInput = null;
                    
                    // Remove watching states
                    characterContainer.classList.remove('watching', 'email-mode', 'password-mode', 'text-mode');
                    
                    // Reset eyes position
                    leftEye.style.transform = 'translate(0, 0)';
                    rightEye.style.transform = 'translate(0, 0)';
                }
            });
            
            field.addEventListener('input', function() {
                if (activeInput === this) {
                    updateEyePosition();
                    
                    // Character blinks occasionally
                    if (Math.random() < 0.1) {
                        characterContainer.classList.add('blinking');
                        setTimeout(() => {
                            characterContainer.classList.remove('blinking');
                        }, 150);
                    }
                    
                    // Adjust mouth based on input length
                    const inputLength = this.value.length;
                    const mouthWidth = Math.min(24 + (inputLength * 1.5), 40);
                    mouth.style.width = `${mouthWidth}px`;
                }
            });
            
            // Track cursor position changes
            field.addEventListener('click', handleSelectionChange);
            field.addEventListener('keyup', handleSelectionChange);
        });
        
        // Listen for selection changes
        document.addEventListener('selectionchange', handleSelectionChange);
        
        // Handle window resize and initial position
        window.addEventListener('resize', calculateEyePosition);
        
        // Toggle password visibility reactions
        const toggleButtons = form.querySelectorAll('.password-toggle');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = this.closest('.input-icon-wrapper').querySelector('input[type="password"], input[type="text"]');
                
                if (passwordField && passwordField.type === 'text') {
                    // Character looks shocked when password is visible
                    characterContainer.classList.add('surprised');
                    setTimeout(() => {
                        characterContainer.classList.remove('surprised');
                    }, 1000);
                } else {
                    characterContainer.classList.remove('surprised');
                }
            });
        });
    });
}); 