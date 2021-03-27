import express from 'express';
import AuthorizationUtil from "../../utils/AuthorizationUtil.js";
const router = express.Router();

router.get("/dashboard", AuthorizationUtil.authRole("admin"), (req, res) => {
res.status(400).end();
});

export default router;