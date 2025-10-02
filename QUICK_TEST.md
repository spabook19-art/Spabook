# 🚀 QUICK CART TEST

## ✅ What Was Fixed:
1. Added **retry mechanism** - tries 10 times to find cart functions
2. Added **initialization logs** - you'll see exactly what's happening
3. Fixed **timing** - waits for both user_id AND script loading

---

## 🧪 TEST NOW:

### Step 1: Hard Refresh
Press **Ctrl + Shift + R** (or Ctrl + F5)

### Step 2: Open Console
Press **F12**, go to **Console** tab

### Step 3: Look for These Logs:
```
🛒 Cart initialized: []
🎯 Cart persistence functions loaded and ready!
✅ Available: {saveCart: "function", loadCart: "function", clearCart: "function"}
🔄 Attempt 1 to load cart...
✅ Found loadCartFromStorage! Loading cart for user: xxxxx
```

---

## 🎯 Quick Console Test:

Paste this in console:
```javascript
// Test 1: Check if functions exist
console.log('Functions:', {
    save: typeof window.saveCartToStorage,
    load: typeof window.loadCartFromStorage,
    clear: typeof window.clearCartFromStorage
});

// Test 2: Check user_id
console.log('User ID:', sessionStorage.getItem('user_id'));

// Test 3: Check cart
console.log('Cart:', window.serviceCart);

// Test 4: Try manual save
window.serviceCart = [{id: 999, name: "Test", price: "100", people: 1, therapists: []}];
window.saveCartToStorage();
console.log('✅ Test cart saved - now refresh page!');
```

After running this, **refresh the page** and the test cart should load!

---

## 🐛 If You See Errors:

### "loadCartFromStorage not available yet"
**Good!** It's trying. Wait for:
```
🔄 Attempt 2 to load cart...
🔄 Attempt 3 to load cart...
✅ Found loadCartFromStorage!
```

### "Failed to load cart after 10 attempts"
**Bad!** modal.js isn't loading. Run this:
```javascript
// Check if modal.js loaded
console.log('Script check:', document.querySelector('script[src*="modal.js"]'));
```

### No emoji logs at all
**Bad!** Hard refresh: **Ctrl + Shift + R**

---

## 📊 Complete Diagnostic:

```javascript
console.log('=== COMPLETE DIAGNOSTIC ===');
console.log('1. Cart in memory:', window.serviceCart);
console.log('2. User ID:', sessionStorage.getItem('user_id'));
console.log('3. Functions:', {
    save: typeof window.saveCartToStorage,
    load: typeof window.loadCartFromStorage,
    clear: typeof window.clearCartFromStorage,
    badge: typeof window.updateCheckoutBadge
});

const userId = sessionStorage.getItem('user_id');
if (userId) {
    const key = `serviceCart_${userId}`;
    const data = localStorage.getItem(key);
    console.log('4. Storage key:', key);
    console.log('5. Saved data:', data);
    if (data) console.log('6. Parsed:', JSON.parse(data));
}

// List all cart-related window properties
console.log('7. All Cart vars:', Object.keys(window).filter(k => k.toLowerCase().includes('cart')));
console.log('=== END DIAGNOSTIC ===');
```

---

## ✅ SUCCESS INDICATORS:

When working, you should see:
1. ✅ `🛒 Cart initialized` 
2. ✅ `🎯 Cart persistence functions loaded`
3. ✅ `✅ Found loadCartFromStorage!`
4. ✅ Badge updates when you add services
5. ✅ Cart persists after page refresh

---

## 🎯 Real Test:

1. **Add a service** (e.g., Relaxing Massage)
2. **Check console** - should see:
   ```
   💾 Saving cart - User ID: xxxxx
   ✅ Cart saved to LocalStorage
   ```
3. **Refresh page** (Ctrl+F5)
4. **Check console** - should see:
   ```
   📂 Loading cart - User ID: xxxxx
   ✅ Cart loaded from LocalStorage
   ```
5. **Check badge** - should show number!

---

**Share the console output if it's still not working!** 🔍