import express from 'express';
const router = express.Router();

router.get('/', (req, res) => {
    if (req.session.loggedIn) return res.redirect("/profile");
    res.redirect("/login");
});

router.get('*', function(req, res){
    res.status(404).send('Unknown page');
});

export default router;