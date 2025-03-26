
--tạo id_account
DELIMITER $$

CREATE TRIGGER before_insert_accounts
BEFORE INSERT ON Accounts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;

    -- Kiểm tra nếu bảng rỗng thì gán id_account = 1
    IF NOT EXISTS (SELECT 1 FROM Accounts) THEN
        SET NEW.id_account = 1;
    ELSE
        -- Tìm giá trị id_account nhỏ nhất bị khuyết
        SELECT MIN(t1.id_account + 1) INTO next_id
        FROM Accounts t1
        WHERE NOT EXISTS (SELECT 1 FROM Accounts t2 WHERE t2.id_account = t1.id_account + 1);

        -- Nếu không có giá trị khuyết, gán id_account = MAX(id_account) + 1
        IF next_id IS NULL THEN
            SELECT MAX(id_account) + 1 INTO next_id FROM Accounts;
        END IF;

        -- Gán giá trị mới cho id_account
        SET NEW.id_account = next_id;
    END IF;
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER before_insert_delivery
BEFORE INSERT ON Delivery_Information
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng trống, bắt đầu từ DEL1
    IF NOT EXISTS (SELECT 1 FROM Delivery_Information WHERE id_delivery LIKE 'DEL%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num -- Để tìm thiếu số 1
            UNION
            SELECT CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) AS id_num
            FROM Delivery_Information
            WHERE id_delivery LIKE 'DEL%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Delivery_Information
            WHERE CAST(SUBSTRING(id_delivery, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị mới cho id_delivery
    SET new_id = CONCAT('DEL', next_id);
    SET NEW.id_delivery = new_id;
END $$

DELIMITER ;


DELIMITER //
CREATE TRIGGER set_shipping_fee
BEFORE INSERT ON Delivery_Information
FOR EACH ROW
BEGIN
    IF NEW.city = 'Cần Thơ' THEN
        SET NEW.shipping_fee = 15000;
    ELSE
        SET NEW.shipping_fee = 30000;
    END IF;
END;
//
DELIMITER ;

--tạo id_order tự động
DELIMITER $$

CREATE TRIGGER before_insert_orders
BEFORE INSERT ON Orders
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có ORD nào, bắt đầu từ ORD1
    IF NOT EXISTS (SELECT 1 FROM Orders WHERE id_order LIKE 'ORD%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu trong chuỗi ORD
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num -- Để tìm thiếu số 1
            UNION
            SELECT CAST(SUBSTRING(id_order, 4) AS UNSIGNED) AS id_num
            FROM Orders
            WHERE id_order LIKE 'ORD%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Orders
            WHERE CAST(SUBSTRING(id_order, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_order mới
    SET new_id = CONCAT('ORD', next_id);
    SET NEW.id_order = new_id;
END $$

DELIMITER ;

-- tạo id_cancel tự động
DELIMITER $$

CREATE TRIGGER before_insert_order_cancellations
BEFORE INSERT ON Order_Cancellations
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE new_id VARCHAR(20);

    -- Nếu bảng chưa có CAN nào, bắt đầu từ CAN1
    IF NOT EXISTS (SELECT 1 FROM Order_Cancellations WHERE id_cancel LIKE 'CAN%') THEN
        SET next_id = 1;
    ELSE
        -- Tìm giá trị nhỏ nhất bị thiếu trong chuỗi CAN
        SELECT MIN(t1.id_num + 1) INTO next_id
        FROM (
            SELECT 0 AS id_num -- Để tìm thiếu số 1
            UNION
            SELECT CAST(SUBSTRING(id_cancel, 4) AS UNSIGNED) AS id_num
            FROM Order_Cancellations
            WHERE id_cancel LIKE 'CAN%'
        ) t1
        WHERE NOT EXISTS (
            SELECT 1 FROM Order_Cancellations
            WHERE CAST(SUBSTRING(id_cancel, 4) AS UNSIGNED) = t1.id_num + 1
        );
    END IF;

    -- Gán giá trị id_cancel mới
    SET new_id = CONCAT('CAN', next_id);
    SET NEW.id_cancel = new_id;
END $$

DELIMITER ;

