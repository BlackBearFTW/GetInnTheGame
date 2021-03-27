import express from 'express';
const router = express.Router();


router.get('*', function(req, res){
    res.status(404).send('Unknown page');
});

export default router;