import express from 'express';
const router = express.Router();

router.get('/', (req, res) => {
    if (req.session.loggedIn) return res.redirect("/profile");
    res.redirect("/login");
});

export default router;