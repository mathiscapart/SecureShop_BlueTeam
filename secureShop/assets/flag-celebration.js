// Universal Flag Celebration Animation System for CTF Challenges
// Author: SecureShop CTF
// This script handles all flag success celebrations across different challenges

(function() {
    'use strict';
    
    // Track if celebration has been shown to prevent duplicates
    let celebrationShown = false;
    
    /**
     * Main celebration function
     * @param {string} flag - The flag string to display
     * @param {string} challengeName - Name of the challenge (e.g., "XSS", "IDOR", "SQL Injection")
     * @param {string} message - Custom success message
     */
    window.showFlagCelebration = function(flag, challengeName = "Challenge", message = "You successfully completed the challenge!") {
        if (celebrationShown) return; // Only show once
        celebrationShown = true;
        
        // Create confetti explosion
        createConfetti();
        
        // Create success overlay
        createSuccessOverlay(flag, challengeName, message);
        
        // Console celebration
        celebrateInConsole(flag, challengeName);
        
        // Play success sound (optional - can be added later)
        // playSuccessSound();
    };
    
    /**
     * Create animated confetti
     */
    function createConfetti() {
        const colors = ['#ff0', '#f0f', '#0ff', '#f00', '#0f0', '#00f', '#ffd700', '#ff69b4'];
        
        for (let i = 0; i < 100; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'flag-confetti';
                confetti.style.cssText = `
                    position: fixed;
                    width: ${Math.random() * 15 + 5}px;
                    height: ${Math.random() * 15 + 5}px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    left: ${Math.random() * 100}%;
                    top: -20px;
                    z-index: 9999;
                    border-radius: 50%;
                    animation: confettiFall ${Math.random() * 3 + 2}s linear;
                    opacity: ${Math.random() * 0.5 + 0.5};
                `;
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 5000);
            }, i * 20);
        }
    }
    
    /**
     * Create the success overlay modal
     */
    function createSuccessOverlay(flag, challengeName, message) {
        const overlay = document.createElement('div');
        overlay.className = 'flag-success-overlay';
        
        // Choose colors based on challenge type
        const challengeColors = {
            'XSS': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'IDOR': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'SQL Injection': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'Admin Access': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'File Upload': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'default': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
        };
        
        const gradient = challengeColors[challengeName] || challengeColors['default'];
        
        overlay.innerHTML = `
            <div class="flag-success-content" style="background: ${gradient};">
                <div class="flag-success-sparkles">✨✨✨</div>
                <div class="flag-success-icon">🎉🏆🎊</div>
                <div class="flag-success-title">🎯 Congratulations!</div>
                <div class="flag-success-challenge">${challengeName} Challenge Completed!</div>
                <div class="flag-success-message">${message}</div>
                <div class="flag-success-flag">🚩 ${flag}</div>
                <div class="flag-success-stats">
                    <div class="flag-stat">
                        <span class="flag-stat-icon">⚡</span>
                        <span class="flag-stat-label">Skill Level</span>
                        <span class="flag-stat-value">Expert</span>
                    </div>
                    <div class="flag-stat">
                        <span class="flag-stat-icon">🎯</span>
                        <span class="flag-stat-label">Status</span>
                        <span class="flag-stat-value">Pwned!</span>
                    </div>
                    <div class="flag-stat">
                        <span class="flag-stat-icon">🔒</span>
                        <span class="flag-stat-label">Security</span>
                        <span class="flag-stat-value">Bypassed</span>
                    </div>
                </div>
                <div class="flag-success-message" style="margin-top: 1rem; font-size: 0.95rem;">
                    <strong>Well done!</strong> You've demonstrated advanced exploitation skills.
                </div>
                <button class="flag-success-close" onclick="this.parentElement.parentElement.remove()">
                    ✨ Awesome! Close ✨
                </button>
                <div class="flag-success-social">
                    <button class="flag-social-btn" onclick="copyFlagToClipboard('${flag}')">
                        📋 Copy Flag
                    </button>
                    <button class="flag-social-btn" onclick="downloadFlagCertificate('${challengeName}', '${flag}')">
                        🏅 Download Certificate
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Add click outside to close
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.remove();
            }
        });
    }
    
    /**
     * Console celebration with styled messages
     */
    function celebrateInConsole(flag, challengeName) {
        console.log('%c🎉🎉🎉 FLAG CAPTURED! 🎉🎉🎉', 
            'color: #00ff00; font-size: 28px; font-weight: bold; text-shadow: 2px 2px 4px #000; padding: 20px;');
        console.log('%c' + challengeName + ' Challenge Completed!', 
            'color: #ffd700; font-size: 20px; font-weight: bold; background: #000; padding: 10px;');
        console.log('%c🚩 ' + flag, 
            'color: #ff6b6b; font-size: 24px; font-weight: bold; background: #000; padding: 15px; border: 3px solid #ffd700;');
        console.log('%cYou are a true hacker! 🔥', 
            'color: #4ecdc4; font-size: 16px; font-weight: bold;');
    }
    
    /**
     * Copy flag to clipboard
     */
    window.copyFlagToClipboard = function(flag) {
        navigator.clipboard.writeText(flag).then(() => {
            alert('✅ Flag copied to clipboard!\n' + flag);
        }).catch(() => {
            prompt('Copy this flag:', flag);
        });
    };
    
    /**
     * Download a certificate (fun feature)
     */
    window.downloadFlagCertificate = function(challengeName, flag) {
        const certificate = `
╔═══════════════════════════════════════════════════════╗
║                                                       ║
║        🏆 SECURESHOP CTF CERTIFICATE 🏆              ║
║                                                       ║
║  This certifies that you have successfully           ║
║  completed the ${challengeName} Challenge!                   ║
║                                                       ║
║  Flag: ${flag}                              ║
║                                                       ║
║  Date: ${new Date().toLocaleDateString()}                                ║
║  Status: PWNED ✅                                    ║
║                                                       ║
║  You've demonstrated advanced cybersecurity skills!  ║
║                                                       ║
╚═══════════════════════════════════════════════════════╝
        `;
        
        const blob = new Blob([certificate], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `SecureShop_${challengeName.replace(/\s+/g, '_')}_Certificate.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };
    
    // Add required CSS styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confettiFall {
            0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        
        @keyframes flagCelebration {
            0% { transform: scale(0) rotate(0deg); opacity: 0; }
            50% { transform: scale(1.2) rotate(180deg); opacity: 1; }
            100% { transform: scale(1) rotate(360deg); opacity: 1; }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes slideInFromTop {
            0% { transform: translateY(-100px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        @keyframes sparkle {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        
        .flag-success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.92);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(10px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .flag-success-content {
            text-align: center;
            padding: 3rem 2rem;
            border-radius: 20px;
            max-width: 700px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.5);
            animation: flagCelebration 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .flag-success-content::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        .flag-success-sparkles {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            animation: sparkle 1.5s infinite;
        }
        
        .flag-success-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: pulse 1s infinite;
            position: relative;
            z-index: 1;
        }
        
        .flag-success-title {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
            font-weight: bold;
            text-shadow: 0 4px 10px rgba(0,0,0,0.5);
            animation: slideInFromTop 0.6s ease-out 0.3s both;
            position: relative;
            z-index: 1;
        }
        
        .flag-success-challenge {
            font-size: 1.5rem;
            color: #ffd700;
            margin-bottom: 1rem;
            font-weight: bold;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
            animation: slideInFromTop 0.6s ease-out 0.4s both;
            position: relative;
            z-index: 1;
        }
        
        .flag-success-flag {
            font-size: 1.8rem;
            color: #ffd700;
            background: rgba(0,0,0,0.4);
            padding: 1.2rem 2rem;
            border-radius: 15px;
            margin: 1.5rem 0;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            border: 3px solid #ffd700;
            box-shadow: 0 0 30px rgba(255,215,0,0.6), inset 0 0 20px rgba(255,215,0,0.2);
            animation: slideInFromTop 0.6s ease-out 0.7s both;
            position: relative;
            z-index: 1;
            word-break: break-all;
        }
        
        .flag-success-message {
            color: white;
            font-size: 1.15rem;
            margin: 1rem 0;
            animation: slideInFromTop 0.6s ease-out 0.9s both;
            position: relative;
            z-index: 1;
            line-height: 1.6;
        }
        
        .flag-success-stats {
            display: flex;
            justify-content: space-around;
            margin: 2rem 0;
            gap: 1rem;
            animation: slideInFromTop 0.6s ease-out 1s both;
            position: relative;
            z-index: 1;
        }
        
        .flag-stat {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 10px;
            flex: 1;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .flag-stat-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .flag-stat-label {
            display: block;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 0.3rem;
        }
        
        .flag-stat-value {
            display: block;
            font-size: 1.1rem;
            color: white;
            font-weight: bold;
        }
        
        .flag-success-close {
            background: white;
            color: #667eea;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s;
            animation: slideInFromTop 0.6s ease-out 1.2s both;
            position: relative;
            z-index: 1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .flag-success-close:hover {
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,255,255,0.4);
        }
        
        .flag-success-close:active {
            transform: scale(1.05);
        }
        
        .flag-success-social {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
            animation: slideInFromTop 0.6s ease-out 1.4s both;
            position: relative;
            z-index: 1;
        }
        
        .flag-social-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }
        
        .flag-social-btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        @media (max-width: 768px) {
            .flag-success-content {
                padding: 2rem 1.5rem;
                width: 95%;
            }
            
            .flag-success-title {
                font-size: 2rem;
            }
            
            .flag-success-challenge {
                font-size: 1.2rem;
            }
            
            .flag-success-flag {
                font-size: 1.3rem;
                padding: 1rem;
            }
            
            .flag-success-stats {
                flex-direction: column;
            }
            
            .flag-success-social {
                flex-direction: column;
            }
        }
    `;
    document.head.appendChild(style);
    
})();
