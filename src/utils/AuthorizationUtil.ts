import express from 'express';
import bcrypt from 'bcryptjs';

class AuthorizationUtil {

    public static authRole(role: "admin" | "user" | "visitor") {
        return (req: express.Request, res: express.Response, next: Function) => {
            if (role == "admin") {
                if (!req.session.loggedIn || !req.session.admin) return res.redirect("/profile");
            } else if (role == "user") {
                if (!req.session.loggedIn) return res.redirect("/login");
            } else if (role === "visitor") {
                if (req.session.loggedIn) return res.redirect("/profile");
            }

            next();
        }

    }

    public static authApi() {
        return (req: express.Request, res: express.Response, next: Function) => {
            if (req.headers.authorization === "AuthGITG") {
                return next();
            } else {
                return res.status(403);
            }

        }
    }

    public static async generateHash(value: string) {
        return await bcrypt.hash(value, 10);
    }

    public static async generateUniqueString() {
        return Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2);
    }
}

export default AuthorizationUtil;