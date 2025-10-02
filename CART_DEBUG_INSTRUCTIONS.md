# 🔍 Cart Persistence Debug Instructions

## Quick Fix Applied
The issue was **timing** - the cart was trying to load before `user_id` was set in sessionStorage.

**✅ FIXED:** Cart now loads after the `user_id_ready` event fires.

---

## 🧪 Testing Steps

### Step 1: Clear Everything & Start Fresh
```javascript
// Open Console (F12), paste and run:
localStorage.clear();
sessionStorage.clear();
console.log('✅ Storage cleared - refresh page now');
```
Then **refresh the page** (Ctrl+F5)

---

### Step 2: Check User ID
```javascript
// Check if user_id is in sessionStorage:
console.log('User ID:', sessionStorage.getItem('user_id'));
```
**Expected:** Should show a UUID like `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`  
**If null:** Supabase session not loaded yet - wait 1 second and try again

---

### Step 3: Add Services to Cart
1. Click on a service (e.g., "Relaxing Massage")
2. Select number of people
3. Click "Add to Cart"
4. **Watch the console for:**
   ```
   💾 Saving cart - User ID: xxxxx
   ✅ Cart saved to LocalStorage: [...]
   📦 Storage Key: serviceCart_xxxxx
   ```

---

### Step 4: Check LocalStorage
```javascript
// View what's saved:
const userId = sessionStorage.getItem('user_id');
const cartKey = `serviceCart_${userId}`;
const savedCart = localStorage.getItem(cartKey);
console.log('📦 Storage Key:', cartKey);
console.log('📄 Saved Cart:', savedCart);
console.log('🛒 Parsed Cart:', JSON.parse(savedCart));
```

---

### Step 5: Test Persistence
1. **Refresh page** (Ctrl+F5)
2. **Watch console for:**
   ```
   📂 Loading cart - User ID: xxxxx
   📦 Storage Key: serviceCart_xxxxx
   📄 Raw saved data: [...]
   ✅ Cart loaded from LocalStorage: [...]
   Cart loaded with items: [...]
   ```
3. **Check badge:** Should show number of services

---

## 🐛 Common Issues & Solutions

### Issue 1: "user_id not found in sessionStorage"
**Cause:** Supabase session not loaded yet  
**Solution:** Wait 1-2 seconds after page load, then try adding to cart

### Issue 2: Cart saves but doesn't load on refresh
**Cause:** Script timing issue  
**Solution:** Check console logs - should see the `user_id_ready` event firing

### Issue 3: "loadCartFromStorage is not a function"
**Cause:** modal.js not loaded  
**Solution:** Check if script tag exists in footer.php

### Issue 4: Different cart key on save vs load
**Cause:** user_id changed between sessions  
**Solution:** Clear storage and re-login:
```javascript
localStorage.clear();
sessionStorage.clear();
```

---

## 📊 Debug Command Reference

### View All LocalStorage
```javascript
console.table(Object.entries(localStorage));
```

### View All SessionStorage
```javascript
console.table(Object.entries(sessionStorage));
```

### View Current Cart in Memory
```javascript
console.log('Memory Cart:', window.serviceCart);
```

### Manually Load Cart
```javascript
if (typeof window.loadCartFromStorage === 'function') {
    window.serviceCart = window.loadCartFromStorage();
    console.log('Loaded:', window.serviceCart);
} else {
    console.error('Function not available');
}
```

### Manually Save Cart
```javascript
if (typeof window.saveCartToStorage === 'function') {
    window.saveCartToStorage();
    console.log('Cart saved');
}
```

### Force Update Badge
```javascript
if (typeof window.updateCheckoutBadge === 'function') {
    window.updateCheckoutBadge();
    console.log('Badge updated');
}
```

---

## ✅ Expected Console Output

### On Page Load:
```
Loading cart for user: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
📂 Loading cart - User ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
📦 Storage Key: serviceCart_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
📄 Raw saved data: [{"id":1,"name":"Relaxing Massage","price":"500",...}]
✅ Cart loaded from LocalStorage: [...]
Cart loaded with items: [...]
```

### When Adding to Cart:
```
Service added with therapists: {...}
💾 Saving cart - User ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
✅ Cart saved to LocalStorage: [...]
📦 Storage Key: serviceCart_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

### When Clearing Cart:
```
🗑️ Clearing cart - User ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
✅ Cart cleared from LocalStorage
📦 Cleared Key: serviceCart_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

---

## 🔧 If Still Not Working

Run this complete diagnostic:
```javascript
// Complete Cart Diagnostic
console.log('=== CART DIAGNOSTIC ===');
console.log('1. User ID:', sessionStorage.getItem('user_id'));
console.log('2. Cart in Memory:', window.serviceCart);
console.log('3. Functions Available:', {
    loadCart: typeof window.loadCartFromStorage,
    saveCart: typeof window.saveCartToStorage,
    clearCart: typeof window.clearCartFromStorage,
    updateBadge: typeof window.updateCheckoutBadge
});

const userId = sessionStorage.getItem('user_id');
if (userId) {
    const cartKey = `serviceCart_${userId}`;
    const savedCart = localStorage.getItem(cartKey);
    console.log('4. LocalStorage Key:', cartKey);
    console.log('5. Saved Cart:', savedCart);
    if (savedCart) {
        console.log('6. Parsed Cart:', JSON.parse(savedCart));
    }
}
console.log('=== END DIAGNOSTIC ===');
```

---

## 💡 Quick Recovery Commands

### If cart is stuck, force reload:
```javascript
window.location.reload(true);
```

### If you need to manually add test data:
```javascript
window.serviceCart = [{
    id: 1,
    name: "Test Service",
    price: "500",
    people: 1,
    therapists: []
}];
window.saveCartToStorage();
window.updateCheckoutBadge();
console.log('Test cart added');
```

---

## 🎯 Success Criteria

✅ Console shows user_id on page load  
✅ Adding service shows "Cart saved to LocalStorage"  
✅ Badge updates with cart count  
✅ Refreshing page shows "Cart loaded from LocalStorage"  
✅ Cart items persist after refresh  
✅ LocalStorage inspector shows `serviceCart_xxxxx` key  
✅ Clear cart removes items and LocalStorage entry  

---

**Need more help?** Share the console output from the diagnostic script above!